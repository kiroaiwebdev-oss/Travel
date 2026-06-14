"""Groq provider (OpenAI-compatible Chat Completions API)."""
from __future__ import annotations

import httpx

from .base import AIProvider, AIProviderError


class GroqProvider(AIProvider):
    name = "groq"
    endpoint = "https://api.groq.com/openai/v1/chat/completions"

    async def chat(self, system: str, user: str) -> str:
        try:
            async with httpx.AsyncClient(timeout=self.timeout) as client:
                resp = await client.post(
                    self.endpoint,
                    headers={"Authorization": f"Bearer {self.api_key}"},
                    json={
                        "model": self.model,
                        "messages": [
                            {"role": "system", "content": system},
                            {"role": "user", "content": user},
                        ],
                        "temperature": 0.4,
                    },
                )
            resp.raise_for_status()
            data = resp.json()
            return data["choices"][0]["message"]["content"].strip()
        except (httpx.HTTPError, KeyError, IndexError) as exc:
            raise AIProviderError(f"groq: {exc}") from exc
