import React from 'react';
import Inbox from "../components/email/inbox";
import {useTemporaryEmailBoxStore} from "../store/temporaryEmailBoxStore";

const EmailInbox = () => {
  const {temporaryEmailBox} = useTemporaryEmailBoxStore();

  return <Inbox temporaryEmailBox={temporaryEmailBox} />;
}

export default EmailInbox;
