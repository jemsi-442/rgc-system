import React from "react";
import { Spinner } from "react-bootstrap";

interface LoaderProps {
  size?: "sm" | "md";
  message?: string;
}

export default function Loader({ size = "md", message }: LoaderProps) {
  const spinnerSize = size === "sm" ? "sm" : undefined;

  return (
    <div className="d-flex flex-column justify-content-center align-items-center py-4">
      <Spinner animation="border" role="status" size={spinnerSize}>
        <span className="visually-hidden">Loading...</span>
      </Spinner>
      {message && <div className="mt-2 text-muted">{message}</div>}
    </div>
  );
}
