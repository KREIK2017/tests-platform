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
    <nav className="sticky top-0 z-50 glass">
      <div className="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between gap-4">
        <Link href="/" className="font-bold text-xl text-foreground flex items-center gap-2 group">
          <div className="h-4 w-4 rounded-full bg-gradient-to-tr from-primary to-pink-300 group-hover:scale-125 transition-transform shadow-sm" />
          {t("app.name")}
        </Link>

        <div className="hidden md:flex items-center gap-1 text-sm font-medium">
          {!loading && user && (
            <>
              <Link
                href="/dashboard"
                className={`px-4 py-2 rounded-full transition-all ${
                  isActive("/dashboard") ? "bg-primary/10 text-primary" : "text-muted hover:text-primary"
                }`}
              >
                {t("nav.dashboard")}
              </Link>
              {user.role === "admin" ? (
                <Link
                  href="/admin/tests"
                  className={`px-4 py-2 rounded-full transition-all ${
                    isActive("/admin/tests") ? "bg-primary/10 text-primary" : "text-muted hover:text-primary"
                  }`}
                >
                  {t("nav.admin_tests")}
                </Link>
              ) : (
                <Link
                  href="/tests"
                  className={`px-4 py-2 rounded-full transition-all ${
                    pathname === "/tests" || pathname.startsWith("/tests/")
                      ? "bg-primary/10 text-primary"
                      : "text-muted hover:text-primary"
                  }`}
                >
                  {t("nav.tests")}
                </Link>
              )}
              <Link
                href="/attempts"
                className={`px-4 py-2 rounded-full transition-all ${
                  isActive("/attempts") ? "bg-primary/10 text-primary" : "text-muted hover:text-primary"
                }`}
              >
                {user.role === "admin" ? t("nav.admin_attempts") : t("nav.my_attempts")}
              </Link>
            </>
          )}
        </div>

        <div className="flex items-center gap-4">
          <div className="inline-flex rounded-full border border-card-border overflow-hidden text-[10px] font-bold tracking-wider ring-1 ring-primary/5">
            {SUPPORTED_LOCALES.map((code) => (
              <button
                key={code}
                type="button"
                onClick={() => setLocale(code as Locale)}
                aria-pressed={locale === code}
                className={`px-3 py-1.5 transition-all ${
                  locale === code
                    ? "bg-primary text-white"
                    : "bg-white text-muted hover:text-primary"
                }`}
              >
                {code.toUpperCase()}
              </button>
            ))}
          </div>

          {loading ? (
            <div className="h-8 w-8 rounded-full border-2 border-primary border-t-transparent animate-spin" />
          ) : user ? (
            <div className="flex items-center gap-4 text-sm">
              <div className="hidden sm:flex flex-col items-end">
                <span className="font-semibold text-foreground leading-none">{user.name}</span>
                <span className="text-[10px] text-muted uppercase tracking-tighter">{user.role}</span>
              </div>
              <button
                type="button"
                onClick={handleLogout}
                className="px-4 py-2 rounded-full border border-card-border font-medium hover:bg-red-50 hover:text-red-600 hover:border-red-100 transition-all active:scale-95"
              >
                {t("nav.logout")}
              </button>
            </div>
          ) : (
            <div className="flex items-center gap-2 text-sm">
              <Link
                href="/login"
                className="px-4 py-2 rounded-full font-medium text-muted hover:text-primary transition-colors"
              >
                {t("nav.login")}
              </Link>
              <Link
                href="/register"
                className="btn-primary"
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
