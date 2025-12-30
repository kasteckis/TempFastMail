import React, {useEffect, useState} from "react";
import {ReceivedEmailResponseDto, ReceivedEmailResponseListDto, TemporaryEmailBox} from "../../types/types";
import axios from "axios";
import {useInterval} from "react-use";

interface Props {
  temporaryEmailBox: TemporaryEmailBox | null;
}

const Inbox = ({temporaryEmailBox}: Props) => {
  const [receivedEmails, setReceivedEmails] = useState<ReceivedEmailResponseListDto[]>([]);
  const [selectedEmail, setSelectedEmail] = useState<ReceivedEmailResponseDto | null>(null);

  const fetchEmails = () => {
    if (temporaryEmailBox === null) {
      return;
    }

    axios.get('/api/email-box/' + temporaryEmailBox.uuid + '/emails')
      .then(r => {
        const emails = r.data as ReceivedEmailResponseListDto[];

        setReceivedEmails(emails);
      });
  }

  const handleSelectEmail = (email: ReceivedEmailResponseListDto) => {
    axios.get('/api/email-box/' + temporaryEmailBox.uuid + '/email/' + email.uuid)
      .then(r => {
        setSelectedEmail(r.data)
      })
  }

  // Fetch emails every 5 seconds
  useInterval(fetchEmails, 5000);

  useEffect(() => {
    fetchEmails();
  }, [temporaryEmailBox])

  return (
    <section className="section">
      <div className="container">
        <div className="columns is-centered">
          <div className="column is-8">
            <div className="box p-0" style={{borderRadius: "12px", overflow: "hidden"}}>

              {/* Header */}
              <div className="columns is-mobile m-0 has-background-dark py-3 px-4">
                <div className="column">
                  <p className="has-text-white has-text-weight-semibold">SENDER</p>
                </div>

                <div className="column is-hidden-mobile">
                  <p className="has-text-white has-text-weight-semibold">SUBJECT</p>
                </div>

                <div className="column is-narrow has-text-right">
                  <p className="has-text-white has-text-weight-semibold">VIEW</p>
                </div>
              </div>

              {selectedEmail === null && receivedEmails.length === 0 && (
                <div className="has-text-centered p-6">
                <span className="icon is-large">
                  <i className="fas fa-sync-alt fa-spin fa-3x has-text-grey-light"></i>
                </span>
                  <p className="title is-5 has-text-grey-dark mt-4">Your inbox is empty</p>
                  <p className="subtitle is-6 has-text-grey">
                    Waiting for incoming emails. Will refresh automatically when new emails arrive.
                  </p>
                </div>
              )}

              {selectedEmail === null && receivedEmails.map(email => {
                return (
                  <div
                    key={email.uuid}
                    className="columns is-mobile m-0 py-3 px-4 is-vcentered is-clickable"
                    style={{ borderBottom: "1px solid #eee" }}
                    onClick={() => handleSelectEmail(email)}
                  >

                    {/* Left block: Sender + email + subject (mobile stacked, desktop columns) */}
                    <div className="column">
                      <p className="has-text-weight-semibold">{email.from_name}</p>

                      {/* email */}
                      <p className="is-size-7 has-text-grey">{email.real_from}</p>

                      {/* Subject visible on mobile only (since tablet has its own column) */}
                      <p className="is-size-7 has-text-grey is-hidden-tablet">
                        {email.subject}
                      </p>
                    </div>

                    {/* Subject column (tablet and desktop only) */}
                    <div className="column is-hidden-mobile">
                      <p className="is-size-6">{email.subject}</p>
                    </div>

                    {/* View icon */}
                    <div className="column is-narrow has-text-right">
                      <span className="icon has-text-grey-dark">
                        <i className="fas fa-envelope-open-text"></i>
                      </span>
                    </div>
                  </div>
                );
              })}

              {selectedEmail && (
                <div className="box p-0" style={{ borderRadius: "12px", overflow: "hidden" }}>
                  <div className="p-4">

                    {/* Header Row */}
                    <div className="is-flex is-justify-content-space-between is-align-items-center mb-4">

                      {/* Back Button */}
                      <button
                        className="button is-light"
                        onClick={() => setSelectedEmail(null)}
                      >
                        <span className="icon">←</span>
                        <span>Back</span>
                      </button>

                      {/* Date */}
                      {selectedEmail.received_at && (
                        <p className="has-text-grey is-size-7">
                          {new Date(selectedEmail.received_at).toLocaleString()}
                        </p>
                      )}
                    </div>

                    {/* Sender */}
                    <p className="has-text-weight-semibold mb-2">
                      {selectedEmail.from_name ? (
                        <>From: {selectedEmail.from_name} {" <"}{selectedEmail.real_from}{">"}</>
                      ) : (
                        <>From: {selectedEmail.real_from}</>
                      )}
                    </p>

                    <hr/>

                    {/* Subject */}
                    <p className="is-size-5 has-text-weight-bold mb-3">
                      {selectedEmail.subject}
                    </p>

                    <hr/>

                    {/* Body */}
                    <div
                      className="content"
                      style={{ whiteSpace: "pre-wrap", overflowX: "auto" }}
                      dangerouslySetInnerHTML={{ __html: selectedEmail.html }}
                    />
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

export default Inbox;
