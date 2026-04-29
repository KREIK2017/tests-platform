export type Role = "admin" | "student";

export interface User {
  id: number;
  name: string;
  email: string;
  role: Role;
  email_verified_at: string | null;
  created_at?: string;
}

export interface Test {
  id: number;
  title: string;
  description: string | null;
  is_published: boolean;
  user_id: number;
  questions_count?: number;
  created_at?: string;
  updated_at?: string;
  author?: User;
  questions?: Question[];
}

export interface Question {
  id: number;
  test_id: number;
  text: string;
  order: number;
  created_at?: string;
  updated_at?: string;
  answers?: Answer[];
}

export interface Answer {
  id: number;
  question_id: number;
  text: string;
  is_correct?: boolean;
  created_at?: string;
  updated_at?: string;
}

export interface AttemptAnswerRecord {
  question_id: number;
  answer_id: number;
}

export interface Attempt {
  id: number;
  user_id: number;
  test_id: number;
  score: number;
  total_questions: number;
  percent: number;
  completed_at: string | null;
  created_at?: string;
  updated_at?: string;
  test?: Test;
  student?: User;
  attempt_answers?: AttemptAnswerRecord[];
}

export interface PaginatedResponse<T> {
  data: T[];
  meta?: {
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
  };
  // Older Laravel paginators put the keys at the root.
  current_page?: number;
  last_page?: number;
  total?: number;
  per_page?: number;
  links?: { url: string | null; label: string; active: boolean }[];
}

export interface SingleResponse<T> {
  data: T;
}

export interface ValidationErrorBody {
  message: string;
  errors?: Record<string, string[]>;
}
