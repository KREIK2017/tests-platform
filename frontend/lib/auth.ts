import Cookies from "js-cookie";

const COOKIE_NAME = "auth_token";

export function getToken(): string | undefined {
  return Cookies.get(COOKIE_NAME);
}

export function setToken(token: string): void {
  Cookies.set(COOKIE_NAME, token, {
    expires: 7,
    sameSite: "Lax",
    secure: typeof window !== "undefined" && window.location.protocol === "https:",
  });
}

export function removeToken(): void {
  Cookies.remove(COOKIE_NAME);
}

export function isAuthenticated(): boolean {
  return Boolean(getToken());
}
