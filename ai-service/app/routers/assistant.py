"""Travel assistant endpoint. Grounds the model with live offers from Laravel."""
from __future__ import annotations

from fastapi import APIRouter

from ..providers.manager import manager
from ..schemas import AssistantRequest, AssistantResponse

router = APIRouter()

SYSTEM_PROMPT = (
    "You are TravelCash's helpful travel assistant. Recommend flights, hotels, "
    "trains, cabs and packages. Be concise and friendly. ALWAYS factor in cashback: "
    "a slightly pricier option with higher cashback can be the better deal. Use ONLY "
    "the provided offers when present; never invent prices. End with one practical tip."
)


def _format_offers(offers) -> str:
    if not offers:
        return "(no live offers were provided for this query)"
    lines = []
    for o in offers[:8]:
        parts = [o.title or o.category or "Offer"]
        if o.provider_name:
            parts.append(f"via {o.provider_name}")
        if o.price is not None:
            parts.append(f"₹{o.price:,.0f}")
        if o.cashback:
            parts.append(f"cashback ₹{o.cashback:,.0f}")
        if o.rating:
            parts.append(f"{o.rating}★")
        lines.append(" · ".join(parts))
    return "\n".join(f"- {line}" for line in lines)


@router.post("/assistant", response_model=AssistantResponse)
async def assistant(req: AssistantRequest) -> AssistantResponse:
    user_prompt = (
        f"Traveller's question: {req.message}\n\n"
        f"Live offers available:\n{_format_offers(req.context)}"
    )

    reply, provider_used = await manager.complete(SYSTEM_PROMPT, user_prompt)

    suggestions = [
        "Cheapest flight from Delhi to Dubai",
        "Best hotel in Goa under ₹5000",
        "Best Thailand family package",
        "Best airport transfer option",
    ]

    return AssistantResponse(message=reply, provider_used=provider_used, suggestions=suggestions)
