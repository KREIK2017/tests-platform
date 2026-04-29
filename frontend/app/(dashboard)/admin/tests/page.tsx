"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { useAuth } from "@/app/context/AuthContext";
import { useTranslation } from "@/app/context/I18nContext";
import api, { extractApiError } from "@/lib/api";
import type { PaginatedResponse, Test } from "@/lib/types";

export default function AdminTestsIndexPage() {
  const { t } = useTranslation();
  const { user, loading: authLoading } = useAuth();
  const [tests, setTests] = useState<Test[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const load = async () => {
    setLoading(true);
    setError(null);
    try {
      const { data } = await api.get<PaginatedResponse<Test>>("/tests");
      setTests(data.data ?? []);
    } catch (err) {
      setError(extractApiError(err).message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (!authLoading && user?.role === "admin") {
      load();
    }
  }, [authLoading, user]);

  const handleDelete = async (test: Test) => {
    if (!window.confirm(t("tests.admin.confirm_delete_test"))) return;
    try {
      await api.delete(`/tests/${test.id}`);
      setTests((prev) => prev.filter((x) => x.id !== test.id));
    } catch (err) {
      setError(extractApiError(err).message);
    }
  };

  if (authLoading) {
    return <div className="p-8 text-slate-500">{t("common.loading")}</div>;
  }
  if (user && user.role !== "admin") {
    return (
      <div className="p-8 text-slate-700">{t("common.error")}: 403</div>
    );
  }

  return (
    <div className="mx-auto max-w-5xl px-4 py-10">
      <div className="flex items-start justify-between flex-wrap gap-3 mb-6">
        <div>
          <h1 className="text-2xl font-semibold">
            {t("tests.admin.index_title")}
          </h1>
          <p className="text-slate-600">{t("tests.admin.index_lead")}</p>
        </div>
        <Link
          href="/admin/tests/create"
          className="rounded bg-indigo-600 text-white px-4 py-2 hover:bg-indigo-700"
        >
          + {t("tests.actions.create")}
        </Link>
      </div>

      {error && (
        <div className="mb-4 rounded bg-red-50 border border-red-200 text-red-700 px-3 py-2 text-sm">
          {error}
        </div>
      )}

      {loading ? (
        <p className="text-slate-500">{t("common.loading")}</p>
      ) : tests.length === 0 ? (
        <div className="rounded border border-slate-200 bg-white p-8 text-center text-slate-500">
          {t("tests.messages.no_tests")}
        </div>
      ) : (
        <div className="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden">
          <table className="w-full text-sm">
            <thead className="bg-slate-50 text-slate-700 text-left">
              <tr>
                <th className="px-4 py-3 font-medium">
                  {t("tests.fields.title")}
                </th>
                <th className="px-4 py-3 font-medium">
                  {t("tests.fields.is_published")}
                </th>
                <th className="px-4 py-3 font-medium text-right">
                  {t("common.actions")}
                </th>
              </tr>
            </thead>
            <tbody>
              {tests.map((test) => (
                <tr key={test.id} className="border-t border-slate-100">
                  <td className="px-4 py-3">
                    <Link
                      href={`/admin/tests/${test.id}`}
                      className="font-medium hover:text-indigo-700"
                    >
                      {test.title}
                    </Link>
                    {typeof test.questions_count === "number" && (
                      <span className="ml-2 text-xs text-slate-500">
                        · {test.questions_count}
                      </span>
                    )}
                  </td>
                  <td className="px-4 py-3">
                    {test.is_published ? (
                      <span className="rounded bg-green-100 text-green-800 text-xs px-2 py-0.5">
                        {t("tests.status.published")}
                      </span>
                    ) : (
                      <span className="rounded bg-slate-100 text-slate-700 text-xs px-2 py-0.5">
                        {t("tests.status.draft")}
                      </span>
                    )}
                  </td>
                  <td className="px-4 py-3 text-right space-x-2">
                    <Link
                      href={`/admin/tests/${test.id}`}
                      className="text-indigo-600 hover:text-indigo-800"
                    >
                      {t("tests.actions.view")}
                    </Link>
                    {user && test.user_id === user.id && (
                      <>
                        <Link
                          href={`/admin/tests/${test.id}/edit`}
                          className="text-slate-600 hover:text-slate-900"
                        >
                          {t("common.edit")}
                        </Link>
                        <button
                          type="button"
                          onClick={() => handleDelete(test)}
                          className="text-red-600 hover:text-red-800"
                        >
                          {t("common.delete")}
                        </button>
                      </>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
