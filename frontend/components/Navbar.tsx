"use client";

import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { useAuth } from "@/app/context/AuthContext";
import { useTranslation } from "@/app/context/I18nContext";
import { SUPPORTED_LOCALES, type Locale } from "@/lib/i18n";

export default function Navbar() {
  const { user, loading, logout } = useAuth();
  const { t, locale, setLocale } = useTranslation();
  const pathname = usePathname();
  const router = useRouter();

  const handleLogout = async () => {
    await logout();
    router.push("/login");
  };

  const isActive = (href: string) =>
    pathname === href || pathname.startsWith(`${href}/`);

  return (
    <nav className="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-slate-200">
      <div className="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between gap-4">
        <Link href="/" className="font-semibold text-slate-900 flex items-center gap-2">
          <span className="text-indigo-600">●</span>
          {t("app.name")}
        </Link>

        <div className="flex items-center gap-1 text-sm">
          {!loading && user && (
            <>
              <Link
                href="/dashboard"
                className={`px-3 py-1.5 rounded ${
                  isActive("/dashboard") ? "bg-slate-100 text-slate-900" : "text-slate-600 hover:text-slate-900"
                }`}
              >
                {t("nav.dashboard")}
              </Link>
              {user.role === "admin" ? (
                <Link
                  href="/admin/tests"
                  className={`px-3 py-1.5 rounded ${
                    isActive("/admin/tests") ? "bg-slate-100 text-slate-900" : "text-slate-600 hover:text-slate-900"
                  }`}
                >
                  {t("nav.admin_tests")}
                </Link>
              ) : (
                <Link
                  href="/tests"
                  className={`px-3 py-1.5 rounded ${
                    pathname === "/tests" || pathname.startsWith("/tests/")
                      ? "bg-slate-100 text-slate-900"
                      : "text-slate-600 hover:text-slate-900"
                  }`}
                >
                  {t("nav.tests")}
                </Link>
              )}
              <Link
                href="/attempts"
                className={`px-3 py-1.5 rounded ${
                  isActive("/attempts") ? "bg-slate-100 text-slate-900" : "text-slate-600 hover:text-slate-900"
                }`}
              >
                {user.role === "admin" ? t("nav.admin_attempts") : t("nav.my_attempts")}
              </Link>
            </>
          )}
        </div>

        <div className="flex items-center gap-3">
          <div className="inline-flex rounded border border-slate-200 overflow-hidden text-xs">
            {SUPPORTED_LOCALES.map((code) => (
              <button
                key={code}
                type="button"
                onClick={() => setLocale(code as Locale)}
                aria-pressed={locale === code}
                className={`px-2 py-1 ${
                  locale === code
                    ? "bg-slate-900 text-white"
                    : "bg-white text-slate-600 hover:bg-slate-50"
                }`}
              >
                {code === "uk" ? "UA" : "EN"}
              </button>
            ))}
          </div>

          {loading ? (
            <span className="text-sm text-slate-400">…</span>
          ) : user ? (
            <div className="flex items-center gap-2 text-sm">
              <span className="text-slate-600 hidden sm:inline">{user.name}</span>
              <button
                type="button"
                onClick={handleLogout}
                className="px-3 py-1.5 rounded border border-slate-200 hover:bg-slate-50"
              >
                {t("nav.logout")}
              </button>
            </div>
          ) : (
            <div className="flex items-center gap-2 text-sm">
              <Link
                href="/login"
                className="px-3 py-1.5 rounded border border-slate-200 hover:bg-slate-50"
              >
                {t("nav.login")}
              </Link>
              <Link
                href="/register"
                className="px-3 py-1.5 rounded bg-indigo-600 text-white hover:bg-indigo-700"
              >
                {t("nav.register")}
              </Link>
            </div>
          )}
        </div>
      </div>
    </nav>
  );
}
