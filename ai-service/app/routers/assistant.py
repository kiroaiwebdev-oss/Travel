"""Travel assistant endpoint. Grounds the model with live offers from Laravel.

System prompt, provider keys, priority and suggestion chips can all be overridden
per-request by the Laravel admin panel, so admins control the assistant end-to-end.
"""
from __future__ import annotations

from fastapi import APIRouter

from ..providers.manager import build_manager
from ..schemas import AssistantRequest, AssistantResponse

router = APIRouter()

DEFAULT_SYSTEM_PROMPT = (
    "You are TripCash's helpful travel assistant. Recommend flights, hotels, "
    "trains, cabs and packages. Be concise and friendly. ALWAYS factor in cashback: "
    "a slightly pricier option with higher cashback can be the better deal. Use ONLY "
    "the provided offers when present; never invent prices. End with one practical tip."
)

DEFAULT_SUGGESTIONS = [
    "Best hotel in Goa under ₹5000",
    "Cheapest flight from Delhi to Dubai",
    "Best Thailand family package",
    "Plan a 3-day North Goa itinerary",
]


def _format_offers(offers) -> str:
    if not offers:
        return "(no live offers were provided for this query)"
    lines = []
    for o in offers[:8]:
        parts = [o.title or o.category or "Offer"]
        if o.provider_name:
            parts.append(f"via {o.provider_name}")
        if o.price is not None:
            parts.append(f"\u20b9{o.price:,.0f}")
        if o.cashback:
            parts.append(f"cashback \u20b9{o.cashback:,.0f}")
        if o.rating:
            parts.append(f"{o.rating}\u2605")
        lines.append(" \u00b7 ".join(parts))
    return "\n".join(f"- {line}" for line in lines)


def _format_history(history) -> str:
    if not history:
        return ""
    turns = []
    for h in history[-6:]:
        role = "User" if h.get("role") == "user" else "Assistant"
        content = (h.get("content") or "").strip()
        if content:
            turns.append(f"{role}: {content}")
    return ("\n\nRecent conversation:\n" + "\n".join(turns)) if turns else ""


@router.post("/assistant", response_model=AssistantResponse)
async def assistant(req: AssistantRequest) -> AssistantResponse:
    user_prompt = (
        f"Traveller's question: {req.message}\n\n"
        f"Live offers available:\n{_format_offers(req.context)}"
        f"{_format_history(req.history)}"
    )

    system = (req.system_prompt or "").strip() or DEFAULT_SYSTEM_PROMPT
    mgr = build_manager(req.keys, req.priority)

    reply, provider_used = await mgr.complete(system, user_prompt)

    suggestions = req.suggestions if req.suggestions else DEFAULT_SUGGESTIONS

    return AssistantResponse(message=reply, provider_used=provider_used, suggestions=suggestions)
