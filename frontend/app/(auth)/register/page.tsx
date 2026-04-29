"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { useAuth } from "@/app/context/AuthContext";
import { useTranslation } from "@/app/context/I18nContext";
import { extractApiError } from "@/lib/api";

const schema = z
  .object({
    name: z.string().min(1).max(255),
    email: z.string().email(),
    password: z.string().min(8),
    password_confirmation: z.string().min(8),
    role: z.enum(["student", "admin"]),
  })
  .refine((v) => v.password === v.password_confirmation, {
    message: "Passwords do not match",
    path: ["password_confirmation"],
  });

type FormValues = z.infer<typeof schema>;

export default function RegisterPage() {
  const { t } = useTranslation();
  const { register: doRegister } = useAuth();
  const router = useRouter();
  const [serverError, setServerError] = useState<string | null>(null);
  const [serverFieldErrors, setServerFieldErrors] =
    useState<Record<string, string[]> | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const {
    register: field,
    handleSubmit,
    formState: { errors },
  } = useForm<FormValues>({
    resolver: zodResolver(schema),
    defaultValues: { role: "student" },
  });

  const onSubmit = async (values: FormValues) => {
    setServerError(null);
    setServerFieldErrors(null);
    setSubmitting(true);
    try {
      await doRegister(
        values.name,
        values.email,
        values.password,
        values.password_confirmation,
        values.role,
      );
      router.push("/verify-email");
    } catch (err) {
      const payload = extractApiError(err);
      setServerError(payload.message);
      if (payload.errors) setServerFieldErrors(payload.errors);
    } finally {
      setSubmitting(false);
    }
  };

  const fieldErrorFor = (key: keyof FormValues): string | undefined => {
    if (errors[key]?.message) return errors[key]?.message as string;
    return serverFieldErrors?.[key]?.[0];
  };

  return (
    <div className="mx-auto max-w-md px-4 py-12">
      <div className="rounded-lg bg-white border border-slate-200 shadow-sm p-6">
        <h1 className="text-2xl font-semibold mb-6 text-center">
          {t("auth.register_title")}
        </h1>

        {serverError && !serverFieldErrors && (
          <div className="mb-4 rounded bg-red-50 border border-red-200 text-red-700 px-3 py-2 text-sm">
            {serverError}
          </div>
        )}

        <form onSubmit={handleSubmit(onSubmit)} className="space-y-4" noValidate>
          <div>
            <label htmlFor="name" className="block text-sm font-medium mb-1">
              {t("auth.name")}
            </label>
            <input
              id="name"
              type="text"
              autoComplete="name"
              {...field("name")}
              className="w-full rounded border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
            {fieldErrorFor("name") && (
              <p className="text-sm text-red-600 mt-1">{fieldErrorFor("name")}</p>
            )}
          </div>

          <div>
            <label htmlFor="email" className="block text-sm font-medium mb-1">
              {t("auth.email")}
            </label>
            <input
              id="email"
              type="email"
              autoComplete="email"
              {...field("email")}
              className="w-full rounded border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
            {fieldErrorFor("email") && (
              <p className="text-sm text-red-600 mt-1">{fieldErrorFor("email")}</p>
            )}
          </div>

          <div>
            <label htmlFor="password" className="block text-sm font-medium mb-1">
              {t("auth.password")}
            </label>
            <input
              id="password"
              type="password"
              autoComplete="new-password"
              {...field("password")}
              className="w-full rounded border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
            {fieldErrorFor("password") && (
              <p className="text-sm text-red-600 mt-1">{fieldErrorFor("password")}</p>
            )}
          </div>

          <div>
            <label
              htmlFor="password_confirmation"
              className="block text-sm font-medium mb-1"
            >
              {t("auth.password_confirmation")}
            </label>
            <input
              id="password_confirmation"
              type="password"
              autoComplete="new-password"
              {...field("password_confirmation")}
              className="w-full rounded border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
            {fieldErrorFor("password_confirmation") && (
              <p className="text-sm text-red-600 mt-1">
                {fieldErrorFor("password_confirmation")}
              </p>
            )}
          </div>

          <div>
            <label htmlFor="role" className="block text-sm font-medium mb-1">
              {t("auth.role")}
            </label>
            <select
              id="role"
              {...field("role")}
              className="w-full rounded border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="student">{t("auth.role_student")}</option>
              <option value="admin">{t("auth.role_admin")}</option>
            </select>
            {fieldErrorFor("role") && (
              <p className="text-sm text-red-600 mt-1">{fieldErrorFor("role")}</p>
            )}
          </div>

          <button
            type="submit"
            disabled={submitting}
            className="w-full rounded bg-indigo-600 text-white py-2.5 font-medium hover:bg-indigo-700 disabled:opacity-50"
          >
            {submitting ? t("common.loading") : t("nav.register")}
          </button>

          <div className="text-center text-sm text-slate-600">
            <Link href="/login" className="hover:text-slate-900">
              {t("auth.already_registered")}
            </Link>
          </div>
        </form>
      </div>
    </div>
  );
}
