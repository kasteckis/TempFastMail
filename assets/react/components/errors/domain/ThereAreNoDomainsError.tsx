const ThereAreNoDomainsError = () => {
  return (
    <div className="columns is-centered">
      <div className="column is-8">
        <div className="notification is-warning has-text-centered">
          There are no domains added. Please add them via the <a target="_blank" href="/admin">admin page</a>.
        </div>
      </div>
    </div>
  );
}

export default ThereAreNoDomainsError;
