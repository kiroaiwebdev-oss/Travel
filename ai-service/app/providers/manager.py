"""AI provider manager — builds the configured providers and runs the fallback chain.

Provider keys + order can come from env (ai-service/.env) OR be overridden per-request
by the Laravel admin panel (so admins manage AI entirely from the dashboard). The first
configured provider that succeeds wins; on error we fall through to the next. If none are
configured/healthy, a deterministic demo responder is used so the assistant always replies.
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
    def __init__(self, keys: dict[str, str] | None = None, priority: list[str] | None = None) -> None:
        keys = keys or {}
        openai_key = (keys.get("openai") or settings.openai_api_key or "").strip()
        gemini_key = (keys.get("gemini") or settings.gemini_api_key or "").strip()
        groq_key = (keys.get("groq") or settings.groq_api_key or "").strip()

        self._registry: dict[str, AIProvider] = {
            "openai": OpenAIProvider(openai_key, settings.openai_model, settings.ai_timeout),
            "gemini": GeminiProvider(gemini_key, settings.gemini_model, settings.ai_timeout),
            "groq": GroqProvider(groq_key, settings.groq_model, settings.ai_timeout),
        }

        if priority:
            self._priority = [p.strip().lower() for p in priority if p and p.strip()]
        else:
            self._priority = settings.priority_list()

    def chain(self) -> list[AIProvider]:
        """Ordered list of usable providers per the configured priority."""
        ordered = []
        for name in self._priority:
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
            "Here are smart picks based on live TripCash offers. "
            "Sort by 'Highest cashback' to maximise your wallet, or 'Best value' to "
            "balance price and cashback. Tip: hotels currently carry the highest cashback share. "
            f"(You asked: \"{user[:160]}\")"
        )


# Default manager from env. Admin overrides build a fresh one per request.
manager = AIManager()


def build_manager(keys: dict[str, str] | None, priority: list[str] | None) -> AIManager:
    """Return the shared manager, or a fresh one when admin overrides are supplied."""
    if (keys and any((v or "").strip() for v in keys.values())) or priority:
        return AIManager(keys=keys, priority=priority)
    return manager
