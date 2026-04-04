import React, {useEffect, useState} from 'react';
import Inbox from "../components/email/inbox";
import Generator from "../components/email/generator";
import axios from "axios";
import {ErrorResponseDto, TemporaryEmailBox, ValidateEmailBoxResponseDto} from "../types/types";
import {ErrorCode} from "../components/errors/ErrorCode";
import ThereAreNoDomainsError from "../components/errors/domain/ThereAreNoDomainsError";

const Application = () => {
  const [temporaryEmailBox, setTemporaryEmailBox] = useState<TemporaryEmailBox|null>(null);
  const [fatalError, setFatalError] = useState<ErrorResponseDto|null>(null);

  const handleRegenerateEmail = () => {
    axios.post('/api/email-box')
      .then(r => {
        const generatedEmailBox = r.data as TemporaryEmailBox;
        localStorage.setItem('email', generatedEmailBox.email);
        localStorage.setItem('email-uuid', generatedEmailBox.uuid);

        setTemporaryEmailBox(generatedEmailBox);
    })
      .catch(e => {
        setFatalError(e.response.data);
      });
  }

  useEffect(() => {
    if (temporaryEmailBox === null) {
      const params = new URLSearchParams(window.location.search);
      const impersonateEmail = params.get('impersonate_email');
      const impersonateUuid = params.get('impersonate_uuid');

      if (impersonateEmail && impersonateUuid) {
        localStorage.setItem('email', impersonateEmail);
        localStorage.setItem('email-uuid', impersonateUuid);
        window.history.replaceState({}, '', '/');
        setTemporaryEmailBox({ email: impersonateEmail, uuid: impersonateUuid });
        return;
      }

      const savedEmail = localStorage.getItem("email");
      const savedUuid = localStorage.getItem("email-uuid");

      if (savedEmail === null || savedUuid === null) {
        handleRegenerateEmail();
        return;
      }

      if (savedEmail && savedUuid) {
        axios.post('/api/email-box/validate', {
          email: savedEmail,
          uuid: savedUuid
        }).then((r) => {
          const validationResponse = r.data as ValidateEmailBoxResponseDto;

          if (validationResponse.is_valid) {
            setTemporaryEmailBox({
              email: savedEmail,
              uuid: savedUuid
            });
          } else {
            handleRegenerateEmail();
          }
        })
      }
    }
  }, [])

  if (fatalError !== null) {

    if (fatalError.code === ErrorCode.THERE_ARE_NO_DOMAINS) {
      return <ThereAreNoDomainsError />;
    }
  }

  return (
    <>
      <Generator temporaryEmailBox={temporaryEmailBox} handleRegenerateEmail={handleRegenerateEmail}/>
      <Inbox temporaryEmailBox={temporaryEmailBox} />
    </>
  );
}

export default Application;
