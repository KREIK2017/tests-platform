"use client";

import Link from "next/link";
import { useAuth } from "@/app/context/AuthContext";
import { useTranslation } from "@/app/context/I18nContext";

export default function Home() {
  const { t } = useTranslation();
  const { user } = useAuth();

  return (
    <div className="mx-auto max-w-5xl px-4 py-16">
      <section className="text-center">
        <h1 className="text-4xl sm:text-5xl font-bold tracking-tight">
          {t("welcome.title")}
        </h1>
        <p className="mt-4 text-lg text-slate-600 max-w-2xl mx-auto">
          {t("welcome.subtitle")}
        </p>
        <div className="mt-8 flex items-center justify-center gap-3">
          {user ? (
            <Link
              href="/dashboard"
              className="px-5 py-2.5 rounded bg-indigo-600 text-white hover:bg-indigo-700"
            >
              {t("nav.dashboard")}
            </Link>
          ) : (
            <>
              <Link
                href="/register"
                className="px-5 py-2.5 rounded bg-indigo-600 text-white hover:bg-indigo-700"
              >
                {t("welcome.cta_register")}
              </Link>
              <Link
                href="/login"
                className="px-5 py-2.5 rounded border border-slate-300 hover:bg-slate-100"
              >
                {t("welcome.cta_login")}
              </Link>
            </>
          )}
        </div>
      </section>

      <section className="grid gap-6 mt-16 md:grid-cols-3">
        {(["admin", "student", "i18n"] as const).map((kind) => (
          <div
            key={kind}
            className="rounded-lg bg-white border border-slate-200 p-6 shadow-sm"
          >
            <h3 className="font-semibold text-lg">
              {t(`welcome.feat.${kind}_title`)}
            </h3>
            <p className="mt-2 text-sm text-slate-600">
              {t(`welcome.feat.${kind}_text`)}
            </p>
          </div>
        ))}
      </section>
    </div>
  );
}
