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
    <div className="flex min-h-[calc(100vh-64px)] flex-col justify-center px-6 py-12 lg:px-8 animate-fade-in">
      <div className="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 className="mt-10 text-center text-3xl font-bold leading-9 tracking-tight text-foreground">
          {t("auth_ui.register_title")}
        </h2>
        <p className="mt-2 text-center text-sm text-muted">
          {t("auth_ui.already_registered")}{' '}
          <Link href="/login" className="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
            {t("auth_ui.login_title")}
          </Link>
        </p>
      </div>

      <div className="mt-10 sm:mx-auto sm:w-full sm:max-w-md">
        <div className="rounded-2xl border border-card-border bg-card-bg p-8 shadow-xl">
          {serverError && !serverFieldErrors && (
            <div className="mb-6 rounded-lg bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 text-sm flex items-center gap-3">
              <svg className="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clipRule="evenodd" />
              </svg>
              {serverError}
            </div>
          )}

          <form onSubmit={handleSubmit(onSubmit)} className="space-y-6" noValidate>
            <div>
              <label htmlFor="name" className="block text-sm font-semibold leading-6 text-foreground">
                {t("auth_ui.name")}
              </label>
              <div className="mt-2">
                <input
                  id="name"
                  type="text"
                  {...field("name")}
                  className="block w-full rounded-2xl border-0 bg-background py-2 text-foreground shadow-sm ring-1 ring-inset ring-card-border placeholder:text-muted focus:ring-2 focus:ring-inset focus:ring-primary/50 sm:text-sm sm:leading-6 transition-all"
                />
              </div>
              {fieldErrorFor("name") && (
                <p className="mt-2 text-xs text-red-600">{fieldErrorFor("name")}</p>
              )}
            </div>

            <div>
              <label htmlFor="email" className="block text-sm font-semibold leading-6 text-foreground">
                {t("auth_ui.email")}
              </label>
              <div className="mt-2">
                <input
                  id="email"
                  type="email"
                  {...field("email")}
                  className="block w-full rounded-2xl border-0 bg-background py-2 text-foreground shadow-sm ring-1 ring-inset ring-card-border placeholder:text-muted focus:ring-2 focus:ring-inset focus:ring-primary/50 sm:text-sm sm:leading-6 transition-all"
                />
              </div>
              {fieldErrorFor("email") && (
                <p className="mt-2 text-xs text-red-600">{fieldErrorFor("email")}</p>
              )}
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div>
                <label htmlFor="password" className="block text-sm font-semibold leading-6 text-foreground">
                  {t("auth_ui.password")}
                </label>
                <div className="mt-2">
                  <input
                    id="password"
                    type="password"
                    {...field("password")}
                    className="block w-full rounded-lg border-0 bg-background py-2 text-foreground shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all"
                  />
                </div>
                {fieldErrorFor("password") && (
                  <p className="mt-2 text-xs text-red-600">{fieldErrorFor("password")}</p>
                )}
              </div>

              <div>
                <label htmlFor="password_confirmation" className="block text-sm font-semibold leading-6 text-foreground">
                  {t("auth_ui.password_confirmation")}
                </label>
                <div className="mt-2">
                  <input
                    id="password_confirmation"
                    type="password"
                    {...field("password_confirmation")}
                    className="block w-full rounded-lg border-0 bg-background py-2 text-foreground shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all"
                  />
                </div>
                {fieldErrorFor("password_confirmation") && (
                  <p className="mt-2 text-xs text-red-600">{fieldErrorFor("password_confirmation")}</p>
                )}
              </div>
            </div>

            <div>
              <label htmlFor="role" className="block text-sm font-semibold leading-6 text-foreground">
                {t("auth_ui.role")}
              </label>
              <div className="mt-2">
                <select
                  id="role"
                  {...field("role")}
                  className="block w-full rounded-lg border-0 bg-background py-2 text-foreground shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all"
                >
                  <option value="student">{t("auth_ui.role_student")}</option>
                  <option value="admin">{t("auth_ui.role_admin")}</option>
                </select>
              </div>
              {fieldErrorFor("role") && (
                <p className="mt-2 text-xs text-red-600">{fieldErrorFor("role")}</p>
              )}
            </div>

            <div>
              <button
                type="submit"
                disabled={submitting}
                className="btn-primary w-full flex justify-center items-center gap-2"
              >
                {submitting && (
                  <div className="h-4 w-4 rounded-full border-2 border-white border-t-transparent animate-spin" />
                )}
                {t("nav.register")}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}
