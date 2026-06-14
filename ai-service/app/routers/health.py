from fastapi import APIRouter

from ..config import settings
from ..providers.manager import manager

router = APIRouter()


@router.get("/health")
async def health() -> dict:
    return {
        "status": "ok",
        "providers": manager.status(),
        "priority": settings.priority_list(),
    }
