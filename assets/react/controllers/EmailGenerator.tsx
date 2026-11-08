import React, {useEffect, useState} from 'react';
import Generator from "../components/email/generator";
import ThereAreNoDomainsError from "../components/errors/domain/ThereAreNoDomainsError";
import {ErrorCode} from "../components/errors/ErrorCode";
import {Domain} from "../types/types";
import {regenerateEmailBox, restoreEmailBox, useTemporaryEmailBoxStore} from "../store/temporaryEmailBoxStore";

interface Props {
  isPremium: boolean;
  domains: Domain[];
}

const EmailGenerator = ({isPremium, domains}: Props) => {
  const {temporaryEmailBox, fatalError} = useTemporaryEmailBoxStore();
  const [selectedDomain, setSelectedDomain] = useState<string | null>(null);

  useEffect(() => {
    if (temporaryEmailBox === null) {
      restoreEmailBox();
    }
  }, []);

  if (fatalError !== null && fatalError.code === ErrorCode.THERE_ARE_NO_DOMAINS) {
    return <ThereAreNoDomainsError />;
  }

  return (
    <Generator
      temporaryEmailBox={temporaryEmailBox}
      handleRegenerateEmail={regenerateEmailBox}
      isPremium={isPremium}
      domains={domains}
      selectedDomain={selectedDomain}
      onSelectedDomainChange={setSelectedDomain}
      errorMessage={fatalError?.error ?? null}
    />
  );
}

export default EmailGenerator;
