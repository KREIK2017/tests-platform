"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { useAuth } from "@/app/context/AuthContext";
import { useTranslation } from "@/app/context/I18nContext";
import api, { extractApiError } from "@/lib/api";
import type { PaginatedResponse, Attempt } from "@/lib/types";

export default function AttemptsIndexPage() {
  const { t } = useTranslation();
  const { user } = useAuth();
  const [attempts, setAttempts] = useState<Attempt[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    (async () => {
      try {
        const { data } = await api.get<PaginatedResponse<Attempt>>("/attempts");
        setAttempts(data.data ?? []);
      } catch (err) {
        setError(extractApiError(err).message);
      } finally {
        setLoading(false);
      }
    })();
  }, []);

  const isAdmin = user?.role === "admin";

  return (
    <div className="mx-auto max-w-5xl px-4 py-12 animate-fade-in">
      <div className="mb-10">
        <h1 className="text-3xl font-bold text-foreground">
          {isAdmin ? t("student.admin_attempts_title") : t("student.attempts_index_title")}
        </h1>
        <p className="text-muted mt-2 max-w-2xl">
          {isAdmin ? t("student.admin_attempts_lead") : t("student.attempts_index_lead")}
        </p>
      </div>

      {error && (
        <div className="mb-8 rounded-3xl bg-red-50 border border-red-100 text-red-600 p-4 text-sm shadow-sm">
          {error}
        </div>
      )}

      {loading ? (
        <div className="flex items-center gap-3 text-muted font-medium">
          <div className="h-5 w-5 border-2 border-primary border-t-transparent rounded-full animate-spin" />
          {t("common.loading")}
        </div>
      ) : attempts.length === 0 ? (
        <div className="rounded-[2.5rem] border border-card-border bg-card-bg p-16 text-center shadow-sm">
          <p className="text-muted font-medium">
            {isAdmin ? t("tests.messages.no_admin_attempts") : t("tests.messages.no_attempts")}
          </p>
        </div>
      ) : (
        <div className="rounded-[2rem] border border-card-border bg-card-bg shadow-sm overflow-hidden">
          <table className="w-full text-sm">
            <thead className="bg-primary/5 text-muted text-left border-b border-card-border">
              <tr>
                {isAdmin && (
                  <th className="px-6 py-4 font-bold uppercase tracking-wider text-[11px]">
                    {t("attempts.fields.student")}
                  </th>
                )}
                <th className="px-6 py-4 font-bold uppercase tracking-wider text-[11px]">
                  {t("attempts.fields.test")}
                </th>
                <th className="px-6 py-4 font-bold uppercase tracking-wider text-[11px]">
                  {t("attempts.fields.result")}
                </th>
                <th className="px-6 py-4 font-bold uppercase tracking-wider text-[11px]">
                  {t("attempts.fields.completed_at")}
                </th>
                <th className="px-6 py-4 font-bold uppercase tracking-wider text-[11px] text-right">
                  {t("common.actions")}
                </th>
              </tr>
            </thead>
            <tbody className="divide-y divide-card-border">
              {attempts.map((attempt) => (
                <tr key={attempt.id} className="table-row-hover">
                  {isAdmin && (
                    <td className="px-6 py-4">
                      <div className="font-bold text-foreground flex items-center gap-2">
                        <div className="h-7 w-7 rounded-full bg-primary/10 flex items-center justify-center text-[10px] text-primary">
                          {attempt.user?.name?.charAt(0).toUpperCase() || "U"}
                        </div>
                        {attempt.user?.name || "user"}
                      </div>
                    </td>
                  )}
                  <td className="px-6 py-4">
                    <div className="text-foreground font-semibold">
                      {attempt.test?.title || "Test"}
                    </div>
                  </td>
                  <td className="px-6 py-4">
                    <span className={`inline-flex items-center rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset ${
                      (attempt.score || 0) / (attempt.total_questions || 1) >= 0.8
                        ? "bg-green-50 text-green-700 ring-green-600/20"
                        : "bg-primary/5 text-primary ring-primary/20"
                    }`}>
                      {attempt.score} / {attempt.total_questions} ({Math.round(((attempt.score || 0) / (attempt.total_questions || 1)) * 100)}%)
                    </span>
                  </td>
                  <td className="px-6 py-4 text-muted text-xs font-medium">
                    {attempt.completed_at 
                      ? new Date(attempt.completed_at).toLocaleString() 
                      : t("tests.student.attempt_status_in_progress")}
                  </td>
                  <td className="px-6 py-4 text-right">
                    <Link
                      href={attempt.completed_at ? `/attempts/${attempt.id}` : `/attempts/${attempt.id}/take`}
                      className="text-primary hover:text-primary-bold font-bold underline-offset-4 hover:underline transition-all"
                    >
                      {attempt.completed_at ? t("tests.actions.view") : t("tests.actions.start")}
                    </Link>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
