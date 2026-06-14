"""Recommendation engine — ranks offers by a cashback-aware value score.

This is the deterministic analytics core; it runs without any AI key and is used
both standalone and to pre-rank offers before they are handed to the assistant.
"""
from __future__ import annotations

from fastapi import APIRouter

from ..schemas import Offer, RecommendRequest, RecommendResponse

router = APIRouter()


def value_score(offer: Offer) -> float:
    price = offer.price or 0.0
    cashback = offer.cashback or 0.0
    rating = offer.rating or 0.0
    net = max(1.0, price - cashback)
    # Reward high cashback ratio and rating; penalise high net price.
    return (cashback / net) * 100 + rating * 5


@router.post("/recommend", response_model=RecommendResponse)
async def recommend(req: RecommendRequest) -> RecommendResponse:
    ranked = sorted(req.offers, key=value_score, reverse=True)[: req.limit]
    rationale = (
        "Ranked by cashback-adjusted value: net price after cashback, weighted by "
        "rating. Higher cashback and ratings rank an option above a marginally "
        "cheaper one."
    )
    return RecommendResponse(ranked=ranked, rationale=rationale)
