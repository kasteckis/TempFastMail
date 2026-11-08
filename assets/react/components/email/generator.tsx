import React, {useState} from "react";
import {Domain, TemporaryEmailBox} from "../../types/types";
import copy from "copy-to-clipboard";

interface Props {
  temporaryEmailBox: TemporaryEmailBox|null;
  handleRegenerateEmail: (domain: string|null) => void;
  isPremium: boolean;
  domains: Domain[];
  selectedDomain: string|null;
  onSelectedDomainChange: (domain: string|null) => void;
  errorMessage: string|null;
}

const Generator = ({
  temporaryEmailBox,
  handleRegenerateEmail,
  isPremium,
  domains,
  selectedDomain,
  onSelectedDomainChange,
  errorMessage,
}: Props) => {
  const [copied, setCopied] = useState(false);

  const canChooseDomain = isPremium && domains.length > 0;

  const handleCopy = () => {
    if (temporaryEmailBox === null) {
      return;
    }

    copy(temporaryEmailBox.email);
    setCopied(true);
  }

  const handleRegenerateButtonPress = () => {
    handleRegenerateEmail(canChooseDomain ? selectedDomain : null);
    setCopied(false);
  }

  const handleDomainChange = (event: React.ChangeEvent<HTMLSelectElement>) => {
    const value = event.target.value;
    onSelectedDomainChange(value === '' ? null : value);
  }

  const regenerateButton = (
    <button className="button is-light" onClick={() => handleRegenerateButtonPress()}>
      <span className="icon"><i className="fas fa-sync-alt"></i></span>
      <span>Regenerate Email</span>
    </button>
  );

  return (
    <>
      <div className="columns is-centered">
        <div className="column is-8">
          <div className="columns is-mobile is-multiline">
            <div className="column is-12-mobile is-9-tablet">
              <label>
                <input
                  className="input is-medium has-text-weight-semibold is-size-6-mobile"
                  type="text"
                  value={temporaryEmailBox === null ? 'Loading ...' : temporaryEmailBox.email}
                  aria-label="Your temporary email address"
                  readOnly
                />
              </label>
            </div>

            <div className="column is-12-mobile is-3-tablet">
              <button className="button is-primary is-medium is-fullwidth" onClick={() => handleCopy()}>
                <span className="icon">
                  <i className={copied ? "fas fa-check" : "fas fa-copy"}></i>
                </span>
                <span>{copied ? 'Copied!' : 'Copy'}</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      {errorMessage !== null && (
        <div className="columns is-centered">
          <div className="column is-8">
            <div className="notification is-danger is-light has-text-centered">{errorMessage}</div>
          </div>
        </div>
      )}

      <div className="buttons is-centered mb-5">
        {canChooseDomain ? (
          <div className="field is-grouped is-grouped-centered">
            <div className="control">
              <div className="select">
                <select value={selectedDomain ?? ''} onChange={handleDomainChange} aria-label="Choose domain">
                  <option value="">Random domain</option>
                  {domains.map((domain) => (
                    <option key={domain.domain} value={domain.domain}>
                      @{domain.domain}{domain.premium ? ' (premium)' : ''}
                    </option>
                  ))}
                </select>
              </div>
            </div>
            <div className="control">
              {regenerateButton}
            </div>
          </div>
        ) : (
          <div className="is-inline">
            {regenerateButton}
          </div>
        )}
      </div>
    </>
  );
}

export default Generator;
