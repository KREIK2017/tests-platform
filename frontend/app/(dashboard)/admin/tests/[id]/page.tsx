"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { use, useEffect, useState } from "react";
import { useAuth } from "@/app/context/AuthContext";
import { useTranslation } from "@/app/context/I18nContext";
import api, { extractApiError } from "@/lib/api";
import type { Question, SingleResponse, Test } from "@/lib/types";

interface Params {
  id: string;
}

export default function AdminTestShowPage({
  params,
}: {
  params: Promise<Params>;
}) {
  const { id } = use(params);
  const testId = Number(id);
  const router = useRouter();
  const { user } = useAuth();
  const { t } = useTranslation();

  const [test, setTest] = useState<Test | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const reload = async () => {
    setLoading(true);
    setError(null);
    try {
      const { data } = await api.get<SingleResponse<Test>>(`/tests/${testId}`);
      setTest(data.data);
    } catch (err) {
      setError(extractApiError(err).message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (Number.isNaN(testId)) return;
    reload();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [testId]);

  const removeTest = async () => {
    if (!window.confirm(t("tests.admin.confirm_delete_test"))) return;
    try {
      await api.delete(`/tests/${testId}`);
      router.push("/admin/tests");
    } catch (err) {
      setError(extractApiError(err).message);
    }
  };

  const removeQuestion = async (q: Question) => {
    if (!window.confirm(t("tests.admin.confirm_delete_question"))) return;
    try {
      await api.delete(`/questions/${q.id}`);
      reload();
    } catch (err) {
      setError(extractApiError(err).message);
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

  if (error || !test) {
    return (
      <div className="mx-auto max-w-3xl px-4 py-10 animate-fade-in">
        <div className="rounded-[2.5rem] bg-red-50 border border-red-100 text-red-600 p-8 text-sm shadow-sm">
          {error ?? t("common.error")}
        </div>
      </div>
    );
  }

  const isOwner = user && user.id === test.user_id;
  const questions = test.questions ?? [];

  return (
    <div className="mx-auto max-w-5xl px-4 py-12 animate-fade-in">
      <div className="mb-10">
        <Link
          href="/admin/tests"
          className="inline-flex items-center text-sm font-bold text-muted hover:text-primary transition-colors mb-6"
        >
          ← {t("tests.admin.index_title")}
        </Link>
        <div className="flex items-start justify-between flex-wrap gap-6">
          <div className="flex-1">
            <h1 className="text-4xl font-extrabold text-foreground tracking-tight mb-3">{test.title}</h1>
            <div className="flex items-center gap-3 flex-wrap">
              {test.is_published ? (
                <span className="rounded-full bg-green-50 text-green-700 text-[10px] font-black uppercase tracking-widest px-3 py-1 border border-green-200">
                  {t("tests.status.published")}
                </span>
              ) : (
                <span className="rounded-full bg-background text-muted text-[10px] font-black uppercase tracking-widest px-3 py-1 border border-card-border">
                  {t("tests.status.draft")}
                </span>
              )}
              <span className="text-xs font-bold text-muted uppercase tracking-wider opacity-60">
                Автор: {test.author?.name}
              </span>
            </div>
          </div>
          {isOwner && (
            <div className="flex gap-3">
              <Link
                href={`/admin/tests/${test.id}/edit`}
                className="btn-secondary py-2 px-6"
              >
                {t("tests.actions.edit")}
              </Link>
              <button
                type="button"
                onClick={removeTest}
                className="rounded-full border border-red-100 bg-red-50 px-6 py-2 text-sm font-bold text-red-600 hover:bg-red-100 transition-all active:scale-95 shadow-sm"
              >
                {t("tests.actions.delete")}
              </button>
            </div>
          )}
        </div>
      </div>

      <div className="grid gap-10 lg:grid-cols-3">
        <div className="lg:col-span-1">
          {test.description && (
            <div className="test-card !p-8 sticky top-24">
              <h2 className="text-xs font-black uppercase tracking-[0.2em] text-primary mb-4 opacity-70">
                {t("tests.fields.description")}
              </h2>
              <p className="text-foreground font-medium leading-relaxed whitespace-pre-line">{test.description}</p>
            </div>
          )}
        </div>

        <div className="lg:col-span-2">
          <div className="test-card !p-0 overflow-hidden">
            <div className="flex items-center justify-between p-8 border-b border-primary/5 bg-primary/[0.02]">
              <h2 className="text-xl font-bold text-foreground">
                {t("tests.admin.questions_block")}{" "}
                <span className="text-sm font-black text-primary ml-2 bg-primary/10 px-2 py-0.5 rounded-lg">
                  {questions.length}
                </span>
              </h2>
              {isOwner && (
                <Link
                  href={`/admin/tests/${test.id}/questions/create`}
                  className="btn-primary py-2 px-6 !text-xs"
                >
                  + {t("tests.actions.add_question")}
                </Link>
              )}
            </div>

            {questions.length === 0 ? (
              <div className="p-16 text-center">
                <p className="text-muted font-medium">
                  {t("tests.messages.no_questions")}
                </p>
              </div>
            ) : (
              <ul className="divide-y divide-primary/5">
                {questions.map((q, i) => (
                  <li key={q.id} className="p-8 hover:bg-primary/[0.01] transition-colors">
                    <div className="flex items-start justify-between gap-6">
                      <div className="flex-1">
                        <div className="flex items-start gap-4">
                          <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary font-black text-xs">
                            {i + 1}
                          </span>
                          <div className="text-xl font-bold text-foreground leading-snug pt-0.5">
                            {q.text}
                          </div>
                        </div>
                        
                        {q.answers && q.answers.length > 0 && (
                          <ul className="mt-6 ml-12 space-y-3">
                            {q.answers.map((a) => (
                              <li
                                key={a.id}
                                className={`flex items-center gap-3 text-sm font-medium transition-colors ${
                                  a.is_correct
                                    ? "text-green-700 font-bold"
                                    : "text-muted"
                                }`}
                              >
                                <div className={`h-2 w-2 rounded-full ${
                                  a.is_correct ? "bg-green-500 shadow-sm shadow-green-200" : "bg-card-border"
                                }`} />
                                {a.text}
                                {a.is_correct && (
                                  <span className="ml-auto text-[10px] font-black uppercase tracking-widest text-green-600 bg-green-50 px-2 py-0.5 rounded">
                                    Вірно
                                  </span>
                                )}
                              </li>
                            ))}
                          </ul>
                        )}
                      </div>
                      
                      {isOwner && (
                        <div className="flex flex-col gap-2 shrink-0">
                          <Link
                            href={`/admin/tests/${test.id}/questions/${q.id}/edit`}
                            className="inline-flex items-center justify-center h-10 w-10 rounded-full bg-background border border-card-border text-muted hover:text-primary hover:border-primary transition-all shadow-sm"
                            title={t("common.edit")}
                          >
                            <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                              <path strokeLinecap="round" strokeLinejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                          </Link>
                          <button
                            type="button"
                            onClick={() => removeQuestion(q)}
                            className="inline-flex items-center justify-center h-10 w-10 rounded-full bg-red-50/50 border border-red-100 text-red-400 hover:text-red-600 hover:border-red-200 transition-all shadow-sm"
                            title={t("common.delete")}
                          >
                            <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                              <path strokeLinecap="round" strokeLinejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                          </button>
                        </div>
                      )}
                    </div>
                  </li>
                ))}
              </ul>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
