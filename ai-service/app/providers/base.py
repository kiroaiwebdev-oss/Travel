"""Abstract AI provider contract.

Every concrete provider (OpenAI, Gemini, Groq) implements `chat()`. The manager
treats them interchangeably and falls back across them on failure.
"""
from __future__ import annotations

import abc


class AIProviderError(Exception):
    """Raised when a provider call fails so the manager can fall back."""


class AIProvider(abc.ABC):
    name: str = "base"

    def __init__(self, api_key: str, model: str, timeout: int = 25) -> None:
        self.api_key = api_key
        self.model = model
        self.timeout = timeout

    def is_configured(self) -> bool:
        """A provider is usable only if it has an API key."""
        return bool(self.api_key)

    @abc.abstractmethod
    async def chat(self, system: str, user: str) -> str:
        """Return the assistant's text reply, or raise AIProviderError."""
        raise NotImplementedError
