"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useEffect } from "react";
import { useAuth } from "@/app/context/AuthContext";
import { useTranslation } from "@/app/context/I18nContext";

interface CardProps {
  href: string;
  title: string;
  text: string;
  cta: string;
}

function Card({ href, title, text, cta }: CardProps) {
  return (
    <Link
      href={href}
      className="block rounded-lg bg-white border border-slate-200 p-6 shadow-sm hover:shadow-md transition"
    >
      <h3 className="font-semibold text-lg">{title}</h3>
      <p className="mt-2 text-sm text-slate-600">{text}</p>
      <span className="mt-4 inline-block text-indigo-600 text-sm font-medium">
        {cta} →
      </span>
    </Link>
  );
}

export default function DashboardPage() {
  const { user, loading } = useAuth();
  const { t } = useTranslation();
  const router = useRouter();

  useEffect(() => {
    if (!loading && !user) router.push("/login");
    if (!loading && user && user.email_verified_at === null) {
      router.push("/verify-email");
    }
  }, [loading, user, router]);

  if (loading || !user) {
    return (
      <div className="mx-auto max-w-5xl px-4 py-12 text-slate-500">
        {t("common.loading")}
      </div>
    );
  }

  return (
    <div className="mx-auto max-w-5xl px-4 py-10">
      <h1 className="text-3xl font-semibold">
        {t("dashboard.greeting", { name: user.name })}
      </h1>
      <p className="text-slate-600 mt-1">
        {user.role === "admin"
          ? t("dashboard.admin_lead")
          : t("dashboard.student_lead")}
      </p>

      {user.role === "admin" ? (
        <section className="mt-8 grid gap-6 md:grid-cols-3">
          <Card
            href="/admin/tests/create"
            title={t("dashboard.create_test")}
            text={t("dashboard.create_test_lead")}
            cta={t("common.create")}
          />
          <Card
            href="/admin/tests"
            title={t("dashboard.all_tests")}
            text={t("dashboard.all_tests_lead")}
            cta={t("nav.admin_tests")}
          />
          <Card
            href="/attempts"
            title={t("dashboard.attempts")}
            text={t("dashboard.attempts_lead")}
            cta={t("nav.admin_attempts")}
          />
        </section>
      ) : (
        <section className="mt-8 grid gap-6 md:grid-cols-2">
          <Card
            href="/tests"
            title={t("dashboard.available_tests")}
            text={t("dashboard.available_tests_lead")}
            cta={t("nav.tests")}
          />
          <Card
            href="/attempts"
            title={t("dashboard.my_attempts")}
            text={t("dashboard.my_attempts_lead")}
            cta={t("nav.my_attempts")}
          />
        </section>
      )}
    </div>
  );
}
