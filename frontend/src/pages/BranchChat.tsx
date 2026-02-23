import React, { useCallback, useContext, useEffect, useMemo, useState } from "react";
import { Button, Card, Form, Row, Col } from "react-bootstrap";
import { BranchChatAPI, ChurchesAPI } from "../services/apiResources";
import { AuthContext } from "../context/AuthContext";

type ChatMessage = {
  id: number;
  message: string;
  created_at: string;
  user?: { id: number; name: string; role?: string };
  user_id: number;
};

type Church = {
  id: number;
  name: string;
};

export default function BranchChat() {
  const authContext = useContext(AuthContext);
  const [messages, setMessages] = useState<ChatMessage[]>([]);
  const [message, setMessage] = useState("");
  const [churches, setChurches] = useState<Church[]>([]);
  const [churchId, setChurchId] = useState<number | "">("");
  const [loading, setLoading] = useState(false);

  const roleSet = useMemo(() => {
    const roles = [
      ...(authContext?.user?.role ? [authContext.user.role] : []),
      ...((authContext?.user?.roles || []).map((role) => role.name)),
    ];
    return new Set(roles);
  }, [authContext?.user]);

  const isUser = roleSet.has("user") && !roleSet.has("admin") && !roleSet.has("super_admin");

  const loadMessages = useCallback(async () => {
    try {
      const res = await BranchChatAPI.list(churchId || undefined);
      setMessages(Array.isArray(res.data) ? res.data : []);
    } catch {
      setMessages([]);
    }
  }, [churchId]);

  useEffect(() => {
    if (!isUser) {
      ChurchesAPI.list().then((res) => {
        setChurches(Array.isArray(res.data) ? res.data : []);
      }).catch(() => setChurches([]));
    }
  }, [isUser]);

  useEffect(() => {
    loadMessages();
    const interval = window.setInterval(loadMessages, 5000);
    return () => window.clearInterval(interval);
  }, [loadMessages]);

  const onSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!message.trim()) return;

    setLoading(true);
    try {
      await BranchChatAPI.create({ message: message.trim(), church_id: churchId ? Number(churchId) : undefined });
      setMessage("");
      await loadMessages();
    } finally {
      setLoading(false);
    }
  };

  const onDelete = async (id: number) => {
    await BranchChatAPI.delete(id);
    await loadMessages();
  };

  return (
    <div className="container mt-4">
      <h3 className="mb-3">Branch Chat</h3>

      {!isUser && (
        <Row className="mb-3">
          <Col md={6}>
            <Form.Select value={churchId} onChange={(e) => setChurchId(e.target.value === "" ? "" : Number(e.target.value))}>
              <option value="">Select branch</option>
              {churches.map((church) => (
                <option key={church.id} value={church.id}>{church.name}</option>
              ))}
            </Form.Select>
          </Col>
        </Row>
      )}

      <Card className="mb-3">
        <Card.Body style={{ maxHeight: 420, overflowY: "auto" }}>
          {messages.length === 0 ? (
            <div className="text-muted">No messages yet.</div>
          ) : (
            messages.map((item) => (
              <div key={item.id} className="border-bottom py-2">
                <div className="d-flex justify-content-between">
                  <strong>{item.user?.name || `User #${item.user_id}`}</strong>
                  <small>{new Date(item.created_at).toLocaleString()}</small>
                </div>
                <div>{item.message}</div>
                {(authContext?.user?.id === item.user_id || roleSet.has("admin") || roleSet.has("super_admin")) && (
                  <button className="btn btn-link text-danger p-0 mt-1" onClick={() => onDelete(item.id)}>Delete</button>
                )}
              </div>
            ))
          )}
        </Card.Body>
      </Card>

      <Form onSubmit={onSubmit}>
        <Row>
          <Col md={10}>
            <Form.Control
              value={message}
              onChange={(e) => setMessage(e.target.value.replace(/[<>]/g, ""))}
              placeholder="Type your message"
            />
          </Col>
          <Col md={2}>
            <Button type="submit" className="w-100" disabled={loading}>{loading ? "Sending..." : "Send"}</Button>
          </Col>
        </Row>
      </Form>
    </div>
  );
}
