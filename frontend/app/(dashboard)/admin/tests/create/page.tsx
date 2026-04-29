"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";
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

export default function AdminTestCreatePage() {
  const { t } = useTranslation();
  const router = useRouter();
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const {
    register: field,
    handleSubmit,
    formState: { errors },
  } = useForm<FormValues>({
    resolver: zodResolver(schema),
    defaultValues: { is_published: false },
  });

  const onSubmit = async (values: FormValues) => {
    setSubmitting(true);
    setError(null);
    try {
      const { data } = await api.post<SingleResponse<Test>>("/tests", {
        title: values.title,
        description: values.description || null,
        is_published: !!values.is_published,
      });
      router.push(`/admin/tests/${data.data.id}`);
    } catch (err) {
      setError(extractApiError(err).message);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="mx-auto max-w-2xl px-4 py-10">
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-2xl font-semibold">{t("tests.admin.create_title")}</h1>
        <Link
          href="/admin/tests"
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
            placeholder={t("tests.admin.placeholder_title")}
            className="block w-full rounded-2xl border-card-border bg-white py-3 px-4 text-foreground shadow-sm focus:ring-2 focus:ring-primary/40 focus:border-primary outline-none transition-all"
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
            placeholder={t("tests.admin.placeholder_description")}
            className="block w-full rounded-2xl border-card-border bg-white py-3 px-4 text-foreground shadow-sm focus:ring-2 focus:ring-primary/40 focus:border-primary outline-none transition-all"
          />
        </div>

        <div className="flex items-center gap-2">
          <input
            id="is_published"
            type="checkbox"
            {...field("is_published")}
            className="h-4 w-4 text-primary focus:ring-primary"
          />
          <label htmlFor="is_published" className="text-sm">
            {t("tests.fields.is_published")}
          </label>
        </div>

        <div className="flex gap-2">
          <button
            type="submit"
            disabled={submitting}
            className="btn-primary w-full sm:w-auto"
          >
            {submitting ? t("common.loading") : t("common.create")}
          </button>
          <button
            type="button"
            onClick={() => router.back()}
            className="btn-secondary w-full sm:w-auto"
          >
            {t("common.cancel")}
          </button>
        </div>
      </form>
    </div>
  );
}
