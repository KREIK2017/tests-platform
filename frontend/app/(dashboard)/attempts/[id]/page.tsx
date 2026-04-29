"use client";

import Link from "next/link";
import { use, useEffect, useState } from "react";
import { useTranslation } from "@/app/context/I18nContext";
import api, { extractApiError } from "@/lib/api";
import type { SingleResponse, Attempt } from "@/lib/types";

interface Params {
  id: string;
}

export default function AttemptShowPage({
  params,
}: {
  params: Promise<Params>;
}) {
  const { id } = use(params);
  const attemptId = Number(id);
  const { t } = useTranslation();

  const [attempt, setAttempt] = useState<Attempt | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    (async () => {
      try {
        const { data } = await api.get<SingleResponse<Attempt>>(`/attempts/${attemptId}`);
        setAttempt(data.data);
      } catch (err) {
        setError(extractApiError(err).message);
      } finally {
        setLoading(false);
      }
    })();
  }, [attemptId]);

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
      <div className="mx-auto max-w-3xl px-4 py-12 animate-fade-in">
        <div className="rounded-[2.5rem] bg-red-50 border border-red-100 text-red-600 p-8 text-sm shadow-sm">
          {error ?? t("common.error")}
        </div>
      </div>
    );
  }

  const questions = attempt.test.questions ?? [];
  const attemptAnswers = attempt.attempt_answers ?? [];

  return (
    <div className="mx-auto max-w-4xl px-4 py-16 animate-fade-in">
      <div className="mb-12">
        <Link 
          href="/attempts" 
          className="inline-flex items-center text-sm font-bold text-muted hover:text-primary transition-colors mb-6"
        >
          ← {t("nav.my_attempts")}
        </Link>
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-8">
          <div>
            <h1 className="text-5xl font-extrabold text-foreground tracking-tight mb-2">{attempt.test.title}</h1>
            <p className="text-muted font-bold uppercase tracking-widest text-xs opacity-60">Студент: {attempt.user?.name || "user"}</p>
          </div>
          <div className="bg-card-bg border border-card-border rounded-[2rem] p-6 px-10 shadow-xl shadow-primary/5 text-center md:text-right">
            <div className={`text-6xl font-black leading-none ${attempt.percent >= 60 ? "text-green-600" : "text-primary"}`}>
              {attempt.percent}%
            </div>
            <div className="text-xs font-black uppercase tracking-[0.2em] text-muted mt-2">
              {t("student.result_score", { score: attempt.score, total: attempt.total_questions })}
            </div>
          </div>
        </div>
      </div>

      <div className="space-y-12">
        {questions.map((q, index) => {
          const userSelection = attemptAnswers.find(aa => aa.question_id === q.id);
          const selectedAnswer = q.answers?.find(a => a.id === userSelection?.answer_id);
          const isCorrect = selectedAnswer?.is_correct;

          return (
            <div
              key={q.id}
              className={`rounded-[2.5rem] border p-10 transition-all shadow-sm ${
                isCorrect ? "border-green-100 bg-green-50/20" : "border-primary/10 bg-primary/[0.02]"
              }`}
            >
              <div className="mb-8 flex items-start gap-4">
                <span className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl font-black text-xs ${
                  isCorrect ? "bg-green-100 text-green-700" : "bg-primary/20 text-primary"
                }`}>
                  {index + 1}
                </span>
                <h3 className="text-2xl font-bold text-foreground leading-snug pt-1">{q.text}</h3>
              </div>

              <div className="ml-14 space-y-3">
                {q.answers?.map((a) => {
                  const isUserChosen = selectedAnswer?.id === a.id;
                  const isCorrectAnswer = a.is_correct;
                  
                  let stateClass = "border-card-border bg-white text-muted";
                  if (isUserChosen) stateClass = isCorrect ? "border-green-500 bg-green-50 text-green-800 ring-2 ring-green-200" : "border-primary bg-primary/5 text-primary ring-2 ring-primary/10";
                  if (isCorrectAnswer && !isUserChosen) stateClass = "border-green-400 bg-green-50/50 text-green-700 font-bold";

                  return (
                    <div
                      key={a.id}
                      className={`flex items-center rounded-2xl border p-4 px-6 text-sm transition-all ${stateClass}`}
                    >
                      <span className="flex-1 font-semibold">{a.text}</span>
                      {isCorrectAnswer && (
                        <span className="ml-3 flex h-6 w-6 items-center justify-center rounded-full bg-green-500 text-white text-[10px]">✓</span>
                      )}
                      {isUserChosen && !isCorrect && (
                        <span className="ml-3 flex h-6 w-6 items-center justify-center rounded-full bg-primary text-white text-[10px]">✕</span>
                      )}
                    </div>
                  );
                })}
              </div>

              <div className="mt-8 ml-14 flex items-center gap-2">
                <div className={`text-xs font-black uppercase tracking-widest px-4 py-1.5 rounded-full border ${
                  isCorrect ? "bg-green-50 text-green-700 border-green-200" : "bg-primary/10 text-primary border-primary/20"
                }`}>
                  {isCorrect ? t("student.answer_correct") : t("student.answer_wrong")}
                </div>
              </div>
            </div>
          );
        })}
      </div>

      <div className="mt-16 text-center">
        <Link
          href="/tests"
          className="btn-primary py-4 px-12 text-lg shadow-2xl"
        >
          {t("nav.tests")}
        </Link>
      </div>
    </div>
  );
}
