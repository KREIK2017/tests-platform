"use client";

import { useRouter } from "next/navigation";
import { use, useEffect, useState } from "react";
import { useTranslation } from "@/app/context/I18nContext";
import api, { extractApiError } from "@/lib/api";
import type { SingleResponse, Attempt, Question } from "@/lib/types";

interface Params {
  id: string;
}

export default function AttemptTakePage({
  params,
}: {
  params: Promise<Params>;
}) {
  const { id } = use(params);
  const attemptId = Number(id);
  const router = useRouter();
  const { t } = useTranslation();

  const [attempt, setAttempt] = useState<Attempt | null>(null);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [answers, setAnswers] = useState<Record<number, number>>({});

  useEffect(() => {
    (async () => {
      try {
        const { data } = await api.get<SingleResponse<Attempt>>(`/attempts/${attemptId}`);
        if (data.data.completed_at) {
          router.push(`/attempts/${attemptId}`);
          return;
        }
        setAttempt(data.data);
      } catch (err) {
        setError(extractApiError(err).message);
      } finally {
        setLoading(false);
      }
    })();
  }, [attemptId, router]);

  const handleAnswerSelect = (questionId: number, answerId: number) => {
    setAnswers((prev) => ({ ...prev, [questionId]: answerId }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    const questions = attempt?.test?.questions ?? [];
    const answeredCount = Object.keys(answers).length;
    
    if (answeredCount < questions.length) {
      if (!window.confirm(t("student.unanswered_warning"))) return;
    }

    setSubmitting(true);
    setError(null);
    try {
      await api.post(`/attempts/${attemptId}/finish`, { answers });
      router.push(`/attempts/${attemptId}`);
    } catch (err) {
      setError(extractApiError(err).message);
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="flex flex-col items-center justify-center min-h-[60vh] gap-4">
        <div className="h-10 w-10 border-4 border-primary border-t-transparent rounded-full animate-spin" />
        <p className="text-muted font-medium">{t("common.loading")}</p>
      </div>
    );
  }

  if (error || !attempt || !attempt.test) {
    return (
      <div className="mx-auto max-w-3xl px-4 py-10">
        <div className="rounded-3xl bg-red-50 border border-red-100 text-red-600 p-6 text-sm shadow-sm">
          {error ?? t("common.error")}
        </div>
      </div>
    );
  }

  const questions = attempt.test.questions ?? [];

  return (
    <div className="mx-auto max-w-3xl px-4 py-12 animate-fade-in">
      <div className="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
          <h1 className="text-3xl font-bold text-foreground">{attempt.test.title}</h1>
          <p className="text-muted mt-2 max-w-lg">
            {t("student.take_intro")}
          </p>
        </div>
        <div className="inline-flex items-center gap-2 px-6 py-2 rounded-full bg-primary/10 text-primary font-bold shadow-sm border border-primary/5 self-start md:self-auto">
          <span className="text-xs uppercase tracking-wider opacity-60">Прогрес:</span>
          <span>{Object.keys(answers).length} / {questions.length}</span>
        </div>
      </div>

      <form onSubmit={handleSubmit} className="space-y-10">
        {questions.map((q, index) => (
          <div
            key={q.id}
            className="rounded-[2.5rem] border border-card-border bg-card-bg p-8 shadow-sm transition-all"
          >
            <div className="mb-6 flex items-start gap-4">
              <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-primary/10 text-primary font-bold">
                {index + 1}
              </span>
              <h3 className="text-xl font-semibold text-foreground pt-1 leading-snug">{q.text}</h3>
            </div>

            <div className="space-y-3">
              {q.answers?.map((a) => (
                <label
                  key={a.id}
                  className={`flex cursor-pointer items-center rounded-2xl border p-4 transition-all duration-200 ${
                    answers[q.id] === a.id
                      ? "border-primary bg-primary/5 ring-1 ring-primary/20"
                      : "border-card-border hover:bg-background/50"
                  }`}
                >
                  <div className="relative flex items-center justify-center">
                    <input
                      type="radio"
                      name={`question_${q.id}`}
                      className="peer h-5 w-5 opacity-0 absolute cursor-pointer"
                      checked={answers[q.id] === a.id}
                      onChange={() => handleAnswerSelect(q.id, a.id)}
                    />
                    <div className={`h-5 w-5 rounded-full border-2 transition-all ${
                      answers[q.id] === a.id ? "border-primary bg-primary" : "border-muted/30 bg-transparent"
                    }`}>
                      {answers[q.id] === a.id && (
                        <div className="h-2 w-2 m-1 rounded-full bg-white animate-fade-in" />
                      )}
                    </div>
                  </div>
                  <span className={`ml-4 font-medium transition-colors ${
                    answers[q.id] === a.id ? "text-primary-bold" : "text-muted"
                  }`}>
                    {a.text}
                  </span>
                </label>
              ))}
            </div>
          </div>
        ))}

        <div className="sticky bottom-8 rounded-full border border-card-border bg-background/80 p-3 backdrop-blur-xl shadow-2xl flex items-center justify-between gap-4 max-w-2xl mx-auto ring-1 ring-white/20">
          <p className="text-sm text-primary font-bold ml-6 hidden sm:block">
            {t("student.taking_progress", {
              index: Object.keys(answers).length,
              total: questions.length,
            })}
          </p>
          <button
            type="submit"
            disabled={submitting}
            className="btn-primary w-full sm:w-auto"
          >
            {submitting ? t("common.loading") : t("student.finish_button")}
          </button>
        </div>
      </form>
    </div>
  );
}
