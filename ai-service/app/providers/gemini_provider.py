"""Google Gemini provider (generateContent REST API)."""
from __future__ import annotations

import httpx

from .base import AIProvider, AIProviderError


class GeminiProvider(AIProvider):
    name = "gemini"

    @property
    def endpoint(self) -> str:
        return (
            f"https://generativelanguage.googleapis.com/v1beta/models/"
            f"{self.model}:generateContent"
        )

    async def chat(self, system: str, user: str) -> str:
        try:
            async with httpx.AsyncClient(timeout=self.timeout) as client:
                resp = await client.post(
                    self.endpoint,
                    params={"key": self.api_key},
                    json={
                        "system_instruction": {"parts": [{"text": system}]},
                        "contents": [{"role": "user", "parts": [{"text": user}]}],
                        "generationConfig": {"temperature": 0.4},
                    },
                )
            resp.raise_for_status()
            data = resp.json()
            return data["candidates"][0]["content"]["parts"][0]["text"].strip()
        except (httpx.HTTPError, KeyError, IndexError) as exc:
            raise AIProviderError(f"gemini: {exc}") from exc
