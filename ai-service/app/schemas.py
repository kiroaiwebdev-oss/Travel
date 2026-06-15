"""Request/response models for the AI service."""
from __future__ import annotations

from pydantic import BaseModel, Field


class Offer(BaseModel):
    provider_name: str | None = None
    category: str | None = None
    title: str | None = None
    price: float | None = None
    cashback: float | None = None
    currency: str | None = "INR"
    rating: float | None = None
    destination: str | None = None


class AssistantRequest(BaseModel):
    message: str = Field(..., min_length=1, max_length=1000)
    context: list[Offer] = Field(default_factory=list)
    user_id: int | None = None
    history: list[dict] = Field(default_factory=list)  # [{role, content}]
    # Admin overrides (configured from the Laravel admin panel)
    system_prompt: str | None = None
    keys: dict[str, str] | None = None       # {groq, gemini, openai}
    priority: list[str] | None = None        # ["groq","gemini","openai"]
    suggestions: list[str] | None = None


class AssistantResponse(BaseModel):
    message: str
    provider_used: str
    suggestions: list[str] = Field(default_factory=list)


class RecommendRequest(BaseModel):
    user_id: int | None = None
    category: str | None = None
    offers: list[Offer] = Field(default_factory=list)
    limit: int = 5


class RecommendResponse(BaseModel):
    ranked: list[Offer]
    rationale: str
