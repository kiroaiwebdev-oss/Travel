# Python AI sidecar (FastAPI) — AI assistant, recommender, workers
FROM python:3.12-slim

ENV PYTHONUNBUFFERED=1 \
    PYTHONDONTWRITEBYTECODE=1 \
    PIP_NO_CACHE_DIR=1

WORKDIR /app

COPY ai-service/requirements.txt ./
RUN pip install --upgrade pip && pip install -r requirements.txt

COPY ai-service/ ./

EXPOSE 8001
CMD ["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "8001", "--workers", "2"]
