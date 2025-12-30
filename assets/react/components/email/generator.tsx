import React, {useState} from "react";
import {TemporaryEmailBox} from "../../types/types";
import copy from "copy-to-clipboard";

interface Props {
  temporaryEmailBox: TemporaryEmailBox|null;
  handleRegenerateEmail: () => void;
}

const Generator = ({temporaryEmailBox, handleRegenerateEmail}: Props) => {
  const [copied, setCopied] = useState(false);

  const handleCopy = () => {
    if (temporaryEmailBox === null) {
      return;
    }

    copy(temporaryEmailBox.email);
    setCopied(true);
  }

  const handleRegenerateButtonPress = () => {
    handleRegenerateEmail();
    setCopied(false);
  }

  return (
    <section className="hero is-dark">
      <div className="hero-body">
        <div className="container">
          <div className="columns is-centered">
            <div className="column is-8">
              <h1 className="title is-5 has-text-centered has-text-white mb-4">
                Your Temporary Email Address
              </h1>

              <div className="columns is-mobile is-multiline">
                <div className="column is-12-mobile is-9-tablet">
                  <label>
                    <input
                      className="input is-medium has-text-weight-semibold is-size-6-mobile"
                      type="text"
                      value={temporaryEmailBox === null ? 'Loading ...' : temporaryEmailBox.email}
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

          <div className="buttons is-centered">
            <div className="is-inline">
              <button className="button is-light" onClick={() => handleRegenerateButtonPress()}>
                <span className="icon"><i className="fas fa-sync-alt"></i></span>
                <span>Regenerate Email</span>
              </button>
            </div>
          </div>

          <div className="columns is-centered">
            <div className="column is-8">
              <p className="has-text-centered has-text-white is-size-6">
                No more spam, marketing emails, or hacker attacks. Keep your real mailbox safe and
                tidy with Temp Fast Mail, a free, temporary, anonymous, and secure email address.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

export default Generator;
