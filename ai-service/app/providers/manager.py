"""AI provider manager — builds the configured providers and runs the fallback chain.

Admin controls the order via AI_PROVIDER_PRIORITY (e.g. groq -> gemini -> openai).
The first configured provider that succeeds wins; on error we fall through to the
next. If none are configured/healthy, a deterministic demo responder is used so the
assistant always returns something useful.
"""
from __future__ import annotations

import logging

from ..config import settings
from .base import AIProvider, AIProviderError
from .gemini_provider import GeminiProvider
from .groq_provider import GroqProvider
from .openai_provider import OpenAIProvider

logger = logging.getLogger("ai.manager")


class AIManager:
    def __init__(self) -> None:
        self._registry: dict[str, AIProvider] = {
            "openai": OpenAIProvider(settings.openai_api_key, settings.openai_model, settings.ai_timeout),
            "gemini": GeminiProvider(settings.gemini_api_key, settings.gemini_model, settings.ai_timeout),
            "groq": GroqProvider(settings.groq_api_key, settings.groq_model, settings.ai_timeout),
        }

    def chain(self) -> list[AIProvider]:
        """Ordered list of usable providers per the configured priority."""
        ordered = []
        for name in settings.priority_list():
            provider = self._registry.get(name)
            if provider and provider.is_configured():
                ordered.append(provider)
        return ordered

    def status(self) -> dict[str, bool]:
        return {name: p.is_configured() for name, p in self._registry.items()}

    async def complete(self, system: str, user: str) -> tuple[str, str]:
        """Try each provider in order. Returns (reply, provider_name)."""
        last_error: AIProviderError | None = None
        for provider in self.chain():
            try:
                reply = await provider.chat(system, user)
                if reply:
                    return reply, provider.name
            except AIProviderError as exc:
                logger.warning("Provider %s failed, falling back: %s", provider.name, exc)
                last_error = exc
                continue

        if last_error:
            logger.error("All AI providers failed: %s", last_error)
        return self._demo_reply(user), "demo"

    @staticmethod
    def _demo_reply(user: str) -> str:
        """Rule-based reply used when no provider key is set (keeps the demo working)."""
        return (
            "Here are smart picks based on live TravelCash offers. "
            "Sort by 'Highest cashback' to maximise your wallet, or 'Best value' to "
            "balance price and cashback. Tip: hotels currently carry the highest cashback share. "
            f"(You asked: \"{user[:160]}\")"
        )


manager = AIManager()
