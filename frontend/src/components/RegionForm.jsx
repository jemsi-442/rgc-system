import React from "react";

const RegionForm = ({ form, setForm, onSubmit }) => {
  return (
    <form onSubmit={onSubmit} className="p-3">

      <div className="mb-3">
        <label className="form-label">Region Name</label>
        <input
          type="text"
          className="form-control"
          value={form.name}
          onChange={(e) => setForm({ ...form, name: e.target.value })}
          required
        />
      </div>

      <div className="mb-3">
        <label className="form-label">Region Code</label>
        <input
          type="text"
          className="form-control"
          value={form.code}
          onChange={(e) => setForm({ ...form, code: e.target.value })}
        />
      </div>

      <button className="btn btn-primary w-100">
        Save Region
      </button>
    </form>
  );
};

export default RegionForm;
