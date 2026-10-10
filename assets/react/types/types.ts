export interface TemporaryEmailBox {
  email: string;
  uuid: string;
}

export interface ReceivedEmailResponseListDto {
  uuid: string;
  from: string;
  real_to: string;
  from_name: string | null;
  subject: string;
  received_at: Date;
}

export interface ReceivedEmailResponseDto {
  uuid: string;
  from: string;
  real_to: string;
  from_name: string | null;
  subject: string;
  html: string | null;
  received_at: Date;
}

export interface ValidateEmailBoxResponseDto {
  is_valid: boolean;
}

export interface ErrorResponseDto {
  code: number;
  error: string;
}

export interface Domain {
  domain: string;
  premium: boolean;
}
