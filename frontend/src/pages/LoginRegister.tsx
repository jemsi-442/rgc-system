import { registerUser, loginUser } from "../services/apiAuth";

// Register
const handleRegister = async (e: React.FormEvent<HTMLFormElement>) => {
  e.preventDefault();
  setError("");
  try {
    const res = await registerUser({
      name: registerData.name,
      email: registerData.email,
      password: registerData.password,
      password_confirmation: registerData.confirmPassword,
    });
    localStorage.setItem("token", res.data.token);
    localStorage.setItem("user", JSON.stringify(res.data.user));
    authContext?.setUser(res.data.user);
    navigate("/dashboard");
  } catch (err: any) {
    setError(err?.response?.data?.message || "Registration failed");
  }
};
