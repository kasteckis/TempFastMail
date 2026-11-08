import {useSyncExternalStore} from "react";
import axios from "axios";
import {ErrorResponseDto, TemporaryEmailBox, ValidateEmailBoxResponseDto} from "../types/types";

interface State {
  temporaryEmailBox: TemporaryEmailBox | null;
  fatalError: ErrorResponseDto | null;
}

let state: State = {temporaryEmailBox: null, fatalError: null};
const listeners = new Set<() => void>();

const setState = (patch: Partial<State>) => {
  state = {...state, ...patch};
  listeners.forEach(listener => listener());
};

const subscribe = (listener: () => void) => {
  listeners.add(listener);

  return () => {
    listeners.delete(listener);
  };
};

export const useTemporaryEmailBoxStore = () => useSyncExternalStore(subscribe, () => state);

export const regenerateEmailBox = (domain: string | null = null) => {
  const payload = domain !== null ? {domain: domain} : undefined;

  axios.post('/api/email-box', payload)
    .then(r => {
      const generatedEmailBox = r.data as TemporaryEmailBox;
      localStorage.setItem('email', generatedEmailBox.email);
      localStorage.setItem('email-uuid', generatedEmailBox.uuid);

      setState({temporaryEmailBox: generatedEmailBox, fatalError: null});
    })
    .catch(e => {
      setState({fatalError: e.response.data});
    });
};

export const restoreEmailBox = () => {
  const params = new URLSearchParams(window.location.search);
  const impersonateEmail = params.get('impersonate_email');
  const impersonateUuid = params.get('impersonate_uuid');

  if (impersonateEmail && impersonateUuid) {
    localStorage.setItem('email', impersonateEmail);
    localStorage.setItem('email-uuid', impersonateUuid);
    window.history.replaceState({}, '', '/');
    setState({temporaryEmailBox: {email: impersonateEmail, uuid: impersonateUuid}});
    return;
  }

  const savedEmail = localStorage.getItem('email');
  const savedUuid = localStorage.getItem('email-uuid');

  if (savedEmail === null || savedUuid === null) {
    regenerateEmailBox();
    return;
  }

  axios.post('/api/email-box/validate', {email: savedEmail, uuid: savedUuid})
    .then(r => {
      const validationResponse = r.data as ValidateEmailBoxResponseDto;

      if (validationResponse.is_valid) {
        setState({temporaryEmailBox: {email: savedEmail, uuid: savedUuid}});
      } else {
        regenerateEmailBox();
      }
    });
};
