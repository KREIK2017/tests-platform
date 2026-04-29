"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { use, useState } from "react";
import { useForm, useFieldArray } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { useTranslation } from "@/app/context/I18nContext";
import api, { extractApiError } from "@/lib/api";

const schema = z.object({
  text: z.string().min(5).max(1000),
  order: z.number().min(0).optional().or(z.literal(0)),
  answers: z.array(
    z.object({
      text: z.string().min(1).max(255),
    })
  ).min(2).max(10),
  correct_answer: z.string(), // index as string for radio button
});

type FormValues = z.infer<typeof schema>;

interface Params {
  id: string;
}

export default function AdminQuestionCreatePage({
  params,
}: {
  params: Promise<Params>;
}) {
  const { id } = use(params);
  const testId = Number(id);
  const router = useRouter();
  const { t } = useTranslation();

  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const {
    register: field,
    handleSubmit,
    control,
    formState: { errors },
  } = useForm<FormValues>({
    resolver: zodResolver(schema),
    defaultValues: {
      text: "",
      answers: [{ text: "" }, { text: "" }, { text: "" }, { text: "" }],
      correct_answer: "0",
    },
  });

  const { fields, append, remove } = useFieldArray({
    control,
    name: "answers",
  });

  const onSubmit = async (values: FormValues) => {
    setSubmitting(true);
    setError(null);
    try {
      await api.post(`/tests/${testId}/questions`, {
        text: values.text,
        order: values.order || null,
        answers: values.answers,
        correct_answer: parseInt(values.correct_answer, 10),
      });
      router.push(`/admin/tests/${testId}`);
    } catch (err) {
      setError(extractApiError(err).message);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="mx-auto max-w-2xl px-4 py-10">
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-2xl font-semibold">
          {t("tests.admin.question_create_title")}
        </h1>
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
        className="space-y-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
      >
        <div>
          <label htmlFor="text" className="block text-sm font-medium mb-1">
            {t("tests.questions.fields.text")}
          </label>
          <textarea
            id="text"
            rows={3}
            {...field("text")}
            placeholder={t("tests.admin.question_text_placeholder")}
            className="w-full rounded border border-slate-300 px-3 py-2"
          />
          {errors.text && (
            <p className="text-sm text-red-600 mt-1">{errors.text.message}</p>
          )}
        </div>

        <div>
          <label htmlFor="order" className="block text-sm font-medium mb-1">
            {t("tests.questions.fields.order")} ({t("tests.admin.order_optional")})
          </label>
          <input
            id="order"
            type="number"
            {...field("order", { valueAsNumber: true })}
            className="w-full rounded border border-slate-300 px-3 py-2"
          />
        </div>

        <hr className="border-slate-100" />

        <div>
          <h3 className="font-medium mb-4">{t("tests.admin.answers_block")}</h3>
          <p className="text-xs text-slate-500 mb-4">
            {t("tests.answers.select_correct")}
          </p>

          <div className="space-y-3">
            {fields.map((f, index) => (
              <div key={f.id} className="flex items-start gap-3">
                <div className="pt-2">
                  <input
                    type="radio"
                    value={index}
                    {...field("correct_answer")}
                    className="h-4 w-4 text-primary focus:ring-primary"
                  />
                </div>
                <div className="flex-1">
                  <input
                    {...field(`answers.${index}.text`)}
                    placeholder={`${t("tests.admin.answer_text_placeholder")} ${
                      index + 1
                    }`}
                    className="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                  />
                  {errors.answers?.[index]?.text && (
                    <p className="text-xs text-red-600 mt-1">
                      {errors.answers[index]?.text?.message}
                    </p>
                  )}
                </div>
                {fields.length > 2 && (
                  <button
                    type="button"
                    onClick={() => remove(index)}
                    className="pt-2 text-slate-400 hover:text-red-600"
                    title={t("common.delete")}
                  >
                    ✕
                  </button>
                )}
              </div>
            ))}
          </div>

          {fields.length < 10 && (
            <button
              type="button"
              onClick={() => append({ text: "" })}
              className="mt-4 text-sm text-primary hover:text-primary-bold font-medium"
            >
              + {t("tests.admin.add_answer")}
            </button>
          )}
        </div>

        <div className="flex gap-2 pt-4">
          <button
            type="submit"
            disabled={submitting}
            className="btn-primary"
          >
            {submitting ? t("common.loading") : t("common.create")}
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
