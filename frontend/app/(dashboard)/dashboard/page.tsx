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
  icon?: React.ReactNode;
}

function Card({ href, title, text, cta, icon }: CardProps) {
  return (
    <Link
      href={href}
      className="group relative flex flex-col rounded-[2.5rem] border border-card-border bg-card-bg p-10 shadow-sm hover:shadow-2xl hover:shadow-primary/5 transition-all duration-500 overflow-hidden"
    >
      <div className="absolute top-0 right-0 p-8 opacity-[0.03] group-hover:opacity-[0.07] transition-opacity">
        <div className="scale-[4] origin-top-right">
          {icon}
        </div>
      </div>
      
      <div className="mb-6 h-14 w-14 flex items-center justify-center rounded-2xl bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white transition-all duration-500 shadow-inner">
        {icon || (
          <svg className="h-7 w-7" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
          </svg>
        )}
      </div>
      <h3 className="font-bold text-2xl text-foreground mb-3 tracking-tight">{title}</h3>
      <p className="text-muted font-medium mb-8 leading-relaxed">{text}</p>
      <div className="mt-auto flex items-center text-sm font-bold text-primary group-hover:translate-x-2 transition-transform duration-300">
        {cta} <span className="ml-2">→</span>
      </div>
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
      <div className="flex flex-col items-center justify-center min-h-[60vh] gap-4">
        <div className="h-12 w-12 border-4 border-primary border-t-transparent rounded-full animate-spin" />
        <p className="text-muted font-medium">{t("common.loading")}</p>
      </div>
    );
  }

  return (
    <div className="mx-auto max-w-7xl px-4 py-16 sm:py-24 animate-fade-in">
      <div className="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16">
        <div>
          <h1 className="text-5xl font-extrabold tracking-tight text-foreground sm:text-6xl">
            {t("dashboard.greeting", { name: user.name })}
          </h1>
          <p className="mt-6 text-xl text-muted max-w-2xl font-medium leading-relaxed">
            {user.role === "admin"
              ? t("dashboard.admin_lead")
              : t("dashboard.student_lead")}
          </p>
        </div>
        <div className="flex items-center gap-3 px-6 py-2.5 rounded-full bg-primary/10 text-primary text-xs font-black uppercase tracking-widest border border-primary/20 shadow-sm self-start md:self-auto ring-1 ring-primary/5">
          <span className="relative flex h-2 w-2">
            <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary/40 opacity-75"></span>
            <span className="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
          </span>
          {user.role}
        </div>
      </div>

      {user.role === "admin" ? (
        <section className="grid gap-10 md:grid-cols-3">
          <Card
            href="/admin/tests/create"
            title={t("dashboard.cards.create_test")}
            text={t("dashboard.cards.create_test_lead")}
            cta={t("common.create")}
            icon={
              <svg className="h-7 w-7" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
              </svg>
            }
          />
          <Card
            href="/admin/tests"
            title={t("dashboard.cards.all_tests")}
            text={t("dashboard.cards.all_tests_lead")}
            cta={t("nav.admin_tests")}
            icon={
              <svg className="h-7 w-7" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .415.162.791.425 1.077m3.277-1.077a2.89 2.89 0 00-.54-.623 2.909 2.909 0 00-2.123-.833 2.909 2.909 0 00-2.122.833 2.89 2.89 0 00-.54.623m6.307 0c.213.733.746 1.33 1.438 1.582m-3.214-1.582a2.91 2.91 0 012.123 1.32m-5.608-1.32a2.908 2.908 0 012.122 1.32m0 0V4.4a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5v2.267m6.707 1.381c.511.133.931.513 1.195 1.033M9.056 21.25c-.347-.313-.556-.758-.556-1.25V4.75c0-.532.224-1.013.586-1.353m12.414 0c.362.34.586.821.586 1.353v15.25c0 .492-.209.937-.556 1.25" />
              </svg>
            }
          />
          <Card
            href="/attempts"
            title={t("dashboard.cards.attempts")}
            text={t("dashboard.cards.attempts_lead")}
            cta={t("nav.admin_attempts")}
            icon={
              <svg className="h-7 w-7" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
              </svg>
            }
          />
        </section>
      ) : (
        <section className="grid gap-10 md:grid-cols-2">
          <Card
            href="/tests"
            title={t("dashboard.cards.available_tests")}
            text={t("dashboard.cards.available_tests_lead")}
            cta={t("nav.tests")}
            icon={
              <svg className="h-7 w-7" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18c-2.305 0-4.408.867-6 2.292m0-14.25v14.25" />
              </svg>
            }
          />
          <Card
            href="/attempts"
            title={t("dashboard.cards.my_attempts")}
            text={t("dashboard.cards.my_attempts_lead")}
            cta={t("nav.my_attempts")}
            icon={
              <svg className="h-7 w-7" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            }
          />
        </section>
      )}
    </div>
  );
}
