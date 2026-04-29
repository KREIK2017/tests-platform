"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { use, useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { useTranslation } from "@/app/context/I18nContext";
import api, { extractApiError } from "@/lib/api";
import type { SingleResponse, Test } from "@/lib/types";

const schema = z.object({
  title: z.string().min(1).max(255),
  description: z.string().max(5000).optional().or(z.literal("")),
  is_published: z.boolean().optional(),
});

type FormValues = z.infer<typeof schema>;

interface Params {
  id: string;
}

export default function AdminTestEditPage({
  params,
}: {
  params: Promise<Params>;
}) {
  const { id } = use(params);
  const testId = Number(id);
  const router = useRouter();
  const { t } = useTranslation();

  const [submitting, setSubmitting] = useState(false);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const {
    register: field,
    handleSubmit,
    reset,
    formState: { errors },
  } = useForm<FormValues>({ resolver: zodResolver(schema) });

  useEffect(() => {
    if (Number.isNaN(testId)) return;
    (async () => {
      try {
        const { data } = await api.get<SingleResponse<Test>>(`/tests/${testId}`);
        reset({
          title: data.data.title,
          description: data.data.description ?? "",
          is_published: data.data.is_published,
        });
      } catch (err) {
        setError(extractApiError(err).message);
      } finally {
        setLoading(false);
      }
    })();
  }, [testId, reset]);

  const onSubmit = async (values: FormValues) => {
    setSubmitting(true);
    setError(null);
    try {
      await api.put(`/tests/${testId}`, {
        title: values.title,
        description: values.description || null,
        is_published: !!values.is_published,
      });
      router.push(`/admin/tests/${testId}`);
    } catch (err) {
      setError(extractApiError(err).message);
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return <div className="p-8 text-slate-500">{t("common.loading")}</div>;
  }

  return (
    <div className="mx-auto max-w-2xl px-4 py-10">
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-2xl font-semibold">{t("tests.admin.edit_title")}</h1>
        <Link
          href={`/admin/tests/${testId}`}
          className="text-sm text-slate-500 hover:text-slate-900"
        >
          ← {t("common.back")}
        </Link>
      </div>

      {error && (
        <div className="mb-4 rounded bg-red-50 border border-red-200 text-red-700 px-3 py-2 text-sm">
          {error}
        </div>
      )}

      <form
        onSubmit={handleSubmit(onSubmit)}
        className="space-y-4 rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
      >
        <div>
          <label htmlFor="title" className="block text-sm font-medium mb-1">
            {t("tests.fields.title")}
          </label>
          <input
            id="title"
            {...field("title")}
            className="w-full rounded border border-slate-300 px-3 py-2"
          />
          {errors.title && (
            <p className="text-sm text-red-600 mt-1">{errors.title.message}</p>
          )}
        </div>

        <div>
          <label htmlFor="description" className="block text-sm font-medium mb-1">
            {t("tests.fields.description")}
          </label>
          <textarea
            id="description"
            rows={4}
            {...field("description")}
            className="w-full rounded border border-slate-300 px-3 py-2"
          />
        </div>

        <div className="flex items-center gap-2">
          <input
            id="is_published"
            type="checkbox"
            {...field("is_published")}
            className="h-4 w-4"
          />
          <label htmlFor="is_published" className="text-sm">
            {t("tests.fields.is_published")}
          </label>
        </div>

        <div className="flex gap-2">
          <button
            type="submit"
            disabled={submitting}
            className="rounded bg-indigo-600 text-white px-4 py-2 hover:bg-indigo-700 disabled:opacity-50"
          >
            {submitting ? t("common.loading") : t("common.save")}
          </button>
          <Link
            href={`/admin/tests/${testId}`}
            className="rounded border border-slate-300 px-4 py-2 hover:bg-slate-50"
          >
            {t("common.cancel")}
          </Link>
        </div>
      </form>
    </div>
  );
}
