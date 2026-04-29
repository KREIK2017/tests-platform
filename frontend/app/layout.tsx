import type { Metadata } from "next";
import { Inter } from "next/font/google";
import "./globals.css";
import Navbar from "@/components/Navbar";
import { AuthProvider } from "@/app/context/AuthContext";
import { I18nProvider } from "@/app/context/I18nContext";

const inter = Inter({
  subsets: ["latin", "cyrillic"],
  display: 'swap',
});

export const metadata: Metadata = {
  title: "Tests Platform | Знання — це сила",
  description: "Сучасна платформа для тестування знань. Створюй тести, перевіряй себе та отримуй результати миттєво.",
};

export default function RootLayout({
  children,
}: Readonly<{ children: React.ReactNode }>) {
  return (
    <html
      lang="uk"
      className={`${inter.className} h-full antialiased`}
    >
      <body className="min-h-screen flex flex-col bg-background text-foreground selection:bg-indigo-100 selection:text-indigo-900 dark:selection:bg-indigo-900 dark:selection:text-indigo-100">
        <I18nProvider>
          <AuthProvider>
            <Navbar />
            <main className="flex-1 relative">
              {children}
            </main>
            <footer className="border-t border-card-border bg-card-bg/50 backdrop-blur-sm py-8">
              <div className="mx-auto max-w-6xl px-4 flex flex-col md:flex-row justify-between items-center gap-4">
                <div className="flex items-center gap-2 font-bold text-muted text-sm">
                  <div className="h-2 w-2 rounded-full bg-indigo-600/50" />
                  Tests Platform
                </div>
                <p className="text-xs text-muted">
                  © {new Date().getFullYear()} Всі права захищено. Створено з ❤️ для тестування знань.
                </p>
              </div>
            </footer>
          </AuthProvider>
        </I18nProvider>
      </body>
    </html>
  );
}
