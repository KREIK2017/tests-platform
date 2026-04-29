"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { use, useEffect, useState } from "react";
import { useTranslation } from "@/app/context/I18nContext";
import api, { extractApiError } from "@/lib/api";
import type { SingleResponse, Test, Attempt } from "@/lib/types";

interface Params {
  id: string;
}

export default function StudentTestShowPage({
  params,
}: {
  params: Promise<Params>;
}) {
  const { id } = use(params);
  const testId = Number(id);
  const router = useRouter();
  const { t } = useTranslation();

  const [test, setTest] = useState<Test | null>(null);
  const [loading, setLoading] = useState(true);
  const [starting, setStarting] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    (async () => {
      try {
        const { data } = await api.get<SingleResponse<Test>>(`/tests/${testId}`);
        setTest(data.data);
      } catch (err) {
        setError(extractApiError(err).message);
      } finally {
        setLoading(false);
      }
    })();
  }, [testId]);

  const handleStart = async () => {
    setStarting(true);
    try {
      const { data } = await api.post<SingleResponse<Attempt>>(`/tests/${testId}/attempts`);
      router.push(`/attempts/${data.data.id}/take`);
    } catch (err) {
      setError(extractApiError(err).message);
    } finally {
      setStarting(false);
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
      <div className="mx-auto max-w-3xl px-4 py-12 animate-fade-in">
        <div className="rounded-[2.5rem] bg-red-50 border border-red-100 text-red-600 p-8 text-sm shadow-sm">
          {error ?? t("common.error")}
        </div>
        <Link href="/tests" className="mt-6 inline-flex items-center text-sm font-bold text-primary hover:underline">
          ← {t("common.back")}
        </Link>
      </div>
    );
  }

  return (
    <div className="mx-auto max-w-4xl px-4 py-16 text-center animate-fade-in">
      <div className="mb-12">
        <h1 className="text-5xl font-extrabold text-foreground tracking-tight mb-4">{test.title}</h1>
        <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 text-primary font-bold text-xs uppercase tracking-widest border border-primary/10 shadow-sm">
           {test.questions_count ?? 0} {t("student.questions_count")}
        </div>
      </div>

      <div className="mx-auto max-w-2xl test-card !p-12 shadow-2xl shadow-primary/5">
        <div className="mb-12 text-left">
          <h3 className="text-xs font-black uppercase tracking-[0.2em] text-primary mb-6 opacity-60">
            {t("tests.fields.description")}
          </h3>
          <p className="text-lg font-medium text-foreground leading-relaxed whitespace-pre-line">
            {test.description || t("tests.messages.no_description")}
          </p>
        </div>

        <div className="border-t border-primary/10 pt-10 flex flex-col gap-4">
          <button
            onClick={handleStart}
            disabled={starting}
            className="btn-primary w-full py-4 text-lg"
          >
            {starting ? t("common.loading") : t("tests.actions.start")}
          </button>
          <Link
            href="/tests"
            className="text-sm font-bold text-muted hover:text-primary transition-colors"
          >
            {t("common.cancel")}
          </Link>
        </div>
      </div>
    </div>
  );
}
