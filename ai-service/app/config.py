"""Runtime configuration loaded from environment variables."""
from __future__ import annotations

from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    model_config = SettingsConfigDict(env_file=".env", extra="ignore")

    openai_api_key: str = ""
    openai_model: str = "gpt-4o-mini"

    gemini_api_key: str = ""
    gemini_model: str = "gemini-1.5-flash"

    groq_api_key: str = ""
    groq_model: str = "llama-3.1-8b-instant"

    # Comma-separated fallback order, e.g. "groq,gemini,openai"
    ai_provider_priority: str = "groq,gemini,openai"
    ai_timeout: int = 25

    def priority_list(self) -> list[str]:
        return [p.strip().lower() for p in self.ai_provider_priority.split(",") if p.strip()]


settings = Settings()
