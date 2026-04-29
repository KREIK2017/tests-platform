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
    return <div className="p-8 text-slate-500">{t("common.loading")}</div>;
  }
  if (error || !test) {
    return (
      <div className="mx-auto max-w-3xl px-4 py-10">
        <div className="rounded bg-red-50 border border-red-200 text-red-700 px-3 py-2 text-sm">
          {error ?? t("common.error")}
        </div>
      </div>
    );
  }

  const isOwner = user && user.id === test.user_id;
  const questions = test.questions ?? [];

  return (
    <div className="mx-auto max-w-4xl px-4 py-10">
      <div className="mb-6">
        <Link
          href="/admin/tests"
          className="text-sm text-slate-500 hover:text-slate-900"
        >
          ← {t("tests.admin.index_title")}
        </Link>
        <div className="flex items-start justify-between flex-wrap gap-3 mt-2">
          <div>
            <h1 className="text-2xl font-semibold">{test.title}</h1>
            <div className="text-sm text-slate-600 mt-1">
              {test.is_published ? (
                <span className="rounded bg-green-100 text-green-800 text-xs px-2 py-0.5 mr-2">
                  {t("tests.status.published")}
                </span>
              ) : (
                <span className="rounded bg-slate-100 text-slate-700 text-xs px-2 py-0.5 mr-2">
                  {t("tests.status.draft")}
                </span>
              )}
              {test.author?.name}
            </div>
          </div>
          {isOwner && (
            <div className="flex gap-2">
              <Link
                href={`/admin/tests/${test.id}/edit`}
                className="rounded border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-50"
              >
                {t("tests.actions.edit")}
              </Link>
              <button
                type="button"
                onClick={removeTest}
                className="rounded border border-red-300 text-red-700 px-3 py-1.5 text-sm hover:bg-red-50"
              >
                {t("tests.actions.delete")}
              </button>
            </div>
          )}
        </div>
      </div>

      {test.description && (
        <div className="mb-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
          <h2 className="text-xs uppercase text-slate-500 font-medium mb-2">
            {t("tests.fields.description")}
          </h2>
          <p className="text-slate-700 whitespace-pre-line">{test.description}</p>
        </div>
      )}

      <div className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div className="flex items-center justify-between mb-4">
          <h2 className="font-semibold">
            {t("tests.admin.questions_block")}{" "}
            <span className="text-xs text-slate-500">({questions.length})</span>
          </h2>
          {isOwner && (
            <Link
              href={`/admin/tests/${test.id}/questions/create`}
              className="rounded bg-indigo-600 text-white text-sm px-3 py-1.5 hover:bg-indigo-700"
            >
              + {t("tests.actions.add_question")}
            </Link>
          )}
        </div>

        {questions.length === 0 ? (
          <p className="text-slate-500 text-sm py-4">
            {t("tests.messages.no_questions")}
          </p>
        ) : (
          <ul className="divide-y divide-slate-100">
            {questions.map((q, i) => (
              <li key={q.id} className="py-3">
                <div className="flex items-start justify-between gap-3">
                  <div>
                    <div className="font-medium">
                      <span className="rounded bg-slate-100 text-slate-700 text-xs px-1.5 py-0.5 mr-2">
                        {i + 1}
                      </span>
                      {q.text}
                    </div>
                    {q.answers && q.answers.length > 0 && (
                      <ul className="mt-2 ml-7 text-sm space-y-1">
                        {q.answers.map((a) => (
                          <li
                            key={a.id}
                            className={
                              a.is_correct
                                ? "text-green-700"
                                : "text-slate-600"
                            }
                          >
                            {a.is_correct ? "✓" : "○"} {a.text}
                          </li>
                        ))}
                      </ul>
                    )}
                  </div>
                  {isOwner && (
                    <div className="flex gap-2 shrink-0">
                      <Link
                        href={`/admin/tests/${test.id}/questions/${q.id}/edit`}
                        className="text-sm text-slate-600 hover:text-slate-900"
                      >
                        {t("common.edit")}
                      </Link>
                      <button
                        type="button"
                        onClick={() => removeQuestion(q)}
                        className="text-sm text-red-600 hover:text-red-800"
                      >
                        {t("common.delete")}
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
  );
}
