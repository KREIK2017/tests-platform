"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { useTranslation } from "@/app/context/I18nContext";
import api, { extractApiError } from "@/lib/api";
import type { PaginatedResponse, Test } from "@/lib/types";

export default function StudentTestsIndexPage() {
  const { t } = useTranslation();
  const [tests, setTests] = useState<Test[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    (async () => {
      try {
        const { data } = await api.get<PaginatedResponse<Test>>("/tests");
        setTests(data.data ?? []);
      } catch (err) {
        setError(extractApiError(err).message);
      } finally {
        setLoading(false);
      }
    })();
  }, []);

  return (
    <div className="mx-auto max-w-6xl px-4 py-12 animate-fade-in">
      <div className="mb-12">
        <h1 className="text-4xl font-bold text-foreground tracking-tight">{t("student.index_title")}</h1>
        <p className="text-muted mt-3 text-lg font-medium">{t("student.index_lead")}</p>
      </div>

      {error && (
        <div className="mb-8 rounded-3xl bg-red-50 border border-red-100 text-red-600 p-4 text-sm shadow-sm">
          {error}
        </div>
      )}

      {loading ? (
        <div className="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
          {[1, 2, 3].map((i) => (
            <div key={i} className="h-64 rounded-[2.5rem] bg-primary/5 animate-pulse" />
          ))}
        </div>
      ) : tests.length === 0 ? (
        <div className="rounded-[2.5rem] border border-card-border bg-card-bg p-16 text-center shadow-sm">
          <p className="text-muted font-medium">{t("tests.messages.no_published_tests")}</p>
        </div>
      ) : (
        <div className="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
          {tests.map((test) => (
            <div
              key={test.id}
              className="group flex flex-col rounded-[2.5rem] border border-card-border bg-card-bg p-8 shadow-sm hover:shadow-2xl hover:shadow-primary/5 transition-all duration-500"
            >
              <h2 className="text-2xl font-bold text-foreground group-hover:text-primary transition-colors duration-300 leading-tight">
                {test.title}
              </h2>
              <p className="mt-4 text-muted font-medium text-sm line-clamp-3 flex-1 leading-relaxed">
                {test.description || t("common.empty")}
              </p>
              
              <div className="mt-8 pt-6 border-t border-primary/10 flex items-center justify-between">
                <div className="flex items-center gap-2">
                   <div className="h-2 w-2 rounded-full bg-primary" />
                   <span className="text-xs font-bold text-muted uppercase tracking-wider">
                    {test.questions_count ?? 0} {t("student.questions_count")}
                   </span>
                </div>
                <Link
                  href={`/tests/${test.id}`}
                  className="btn-primary py-2 px-6"
                >
                  {t("tests.actions.view")}
                </Link>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
