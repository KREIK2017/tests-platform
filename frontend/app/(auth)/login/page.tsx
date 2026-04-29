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

const schema = z.object({
  email: z.string().email(),
  password: z.string().min(1),
});

type FormValues = z.infer<typeof schema>;

export default function LoginPage() {
  const { t } = useTranslation();
  const { login } = useAuth();
  const router = useRouter();
  const [serverError, setServerError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const {
    register: field,
    handleSubmit,
    formState: { errors },
  } = useForm<FormValues>({
    resolver: zodResolver(schema),
  });

  const onSubmit = async (values: FormValues) => {
    setServerError(null);
    setSubmitting(true);
    try {
      await login(values.email, values.password);
      router.push("/dashboard");
    } catch (err) {
      setServerError(extractApiError(err).message);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="flex min-h-[calc(100vh-64px)] flex-col justify-center px-6 py-12 lg:px-8 animate-fade-in">
      <div className="sm:mx-auto sm:w-full sm:max-w-sm">
        <h2 className="mt-10 text-center text-3xl font-bold leading-9 tracking-tight text-foreground">
          {t("auth_ui.login_title")}
        </h2>
        <p className="mt-2 text-center text-sm text-muted">
          {t("auth_ui.no_account")}{' '}
          <Link href="/register" className="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
            {t("auth_ui.register_title")}
          </Link>
        </p>
      </div>

      <div className="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <div className="rounded-2xl border border-card-border bg-card-bg p-8 shadow-xl">
          {serverError && (
            <div className="mb-6 rounded-lg bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 text-sm flex items-center gap-3">
              <svg className="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clipRule="evenodd" />
              </svg>
              {serverError}
            </div>
          )}

          <form onSubmit={handleSubmit(onSubmit)} className="space-y-6" noValidate>
            <div>
              <label htmlFor="email" className="block text-sm font-semibold leading-6 text-foreground">
                {t("auth_ui.email")}
              </label>
              <div className="mt-2">
                <input
                  id="email"
                  type="email"
                  autoComplete="email"
                  {...field("email")}
                  className="block w-full rounded-2xl border-0 bg-background py-2 text-foreground shadow-sm ring-1 ring-inset ring-card-border placeholder:text-muted focus:ring-2 focus:ring-inset focus:ring-primary/50 sm:text-sm sm:leading-6 transition-all"
                />
              </div>
              {errors.email && (
                <p className="mt-2 text-xs text-red-600">{errors.email.message}</p>
              )}
            </div>

            <div>
              <div className="flex items-center justify-between">
                <label htmlFor="password" className="block text-sm font-semibold leading-6 text-foreground">
                  {t("auth_ui.password")}
                </label>
                <div className="text-sm">
                  <Link href="#" className="font-semibold text-indigo-600 hover:text-indigo-500">
                    {t("auth_ui.forgot_password")}
                  </Link>
                </div>
              </div>
              <div className="mt-2">
                <input
                  id="password"
                  type="password"
                  autoComplete="current-password"
                  {...field("password")}
                  className="block w-full rounded-2xl border-0 bg-background py-2 text-foreground shadow-sm ring-1 ring-inset ring-card-border placeholder:text-muted focus:ring-2 focus:ring-inset focus:ring-primary/50 sm:text-sm sm:leading-6 transition-all"
                />
              </div>
              {errors.password && (
                <p className="mt-2 text-xs text-red-600">{errors.password.message}</p>
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
                {t("nav.login")}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}
