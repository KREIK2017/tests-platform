"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { useAuth } from "@/app/context/AuthContext";
import { useTranslation } from "@/app/context/I18nContext";
import api, { extractApiError } from "@/lib/api";

export default function VerifyEmailPage() {
  const { t } = useTranslation();
  const { user, logout } = useAuth();
  const router = useRouter();
  const [resent, setResent] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const resend = async () => {
    setSubmitting(true);
    setError(null);
    try {
      await api.post("/email/verification-notification");
      setResent(true);
    } catch (err) {
      setError(extractApiError(err).message);
    } finally {
      setSubmitting(false);
    }
  };

  const handleLogout = async () => {
    await logout();
    router.push("/login");
  };

  return (
    <div className="mx-auto max-w-md px-4 py-12">
      <div className="rounded-lg bg-white border border-slate-200 shadow-sm p-6">
        <h1 className="text-2xl font-semibold mb-3">{t("auth.verify_title")}</h1>
        <p className="text-slate-600 mb-4">{t("auth.verify_intro")}</p>

        {user && (
          <p className="text-sm text-slate-500 mb-4">
            {user.email}
          </p>
        )}

        {resent && (
          <div className="mb-4 rounded bg-green-50 border border-green-200 text-green-700 px-3 py-2 text-sm">
            {t("auth.verify_resent")}
          </div>
        )}

        {error && (
          <div className="mb-4 rounded bg-red-50 border border-red-200 text-red-700 px-3 py-2 text-sm">
            {error}
          </div>
        )}

        <div className="flex items-center justify-between gap-3">
          <button
            type="button"
            onClick={resend}
            disabled={submitting}
            className="rounded bg-indigo-600 text-white px-4 py-2 hover:bg-indigo-700 disabled:opacity-50"
          >
            {submitting ? t("common.loading") : t("auth.verify_resend")}
          </button>
          <button
            type="button"
            onClick={handleLogout}
            className="text-sm text-slate-500 hover:text-slate-900"
          >
            {t("nav.logout")}
          </button>
        </div>
      </div>
    </div>
  );
}
