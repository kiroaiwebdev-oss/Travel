"""TripCash AI sidecar (FastAPI).

Responsibilities (per architecture): AI travel assistant, recommendation engine,
analytics/data processing. Laravel remains the system of record and main backend.
"""
from __future__ import annotations

import logging

from fastapi import FastAPI

from .routers import assistant, health, recommend

logging.basicConfig(level=logging.INFO)

app = FastAPI(
    title="TripCash AI Service",
    version="1.0.0",
    description="AI assistant, recommender and analytics sidecar for TripCash.",
)

app.include_router(health.router, tags=["health"])
app.include_router(assistant.router, tags=["assistant"])
app.include_router(recommend.router, tags=["recommend"])


@app.get("/")
async def root() -> dict:
    return {"service": "tripcash-ai", "status": "running"}
