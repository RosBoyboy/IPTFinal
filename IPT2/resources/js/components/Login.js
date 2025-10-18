import React, { useState, useEffect } from 'react';
import axios from 'axios';
import '../../sass/_login.scss';
import { FaUserAlt, FaLock, FaSignInAlt } from 'react-icons/fa';

const Login = () => {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  useEffect(() => {
    // Initialize CSRF cookie for Laravel Sanctum
    axios.get('/sanctum/csrf-cookie').catch(() => {});
  }, []);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    try {
      await axios.get('/sanctum/csrf-cookie');
      const resp = await axios.post('/login', { username, password });
      if (resp.data.success) {
        window.location.href = '/dashboard';
      } else {
        setError(resp.data.message || 'Invalid credentials');
      }
    } catch (err) {
      setError(err.response?.data?.message || 'Login failed');
    }
  };

  return (
    <div className="login-page">
      <div className="login-card">
        <div className="login-card-top">
          <h1 className="card-title">SFMS Management<br/>System</h1>
        </div>

        <form className="login-card-body" onSubmit={handleSubmit}>
          <label className="field-label">Username</label>
          <div className="input-group">
            <span className="input-icon"><FaUserAlt /></span>
            <input
              type="text"
              placeholder="Enter username"
              value={username}
              onChange={(e) => setUsername(e.target.value)}
              required
            />
          </div>

          <label className="field-label">Password</label>
          <div className="input-group">
            <span className="input-icon"><FaLock /></span>
            <input
              type="password"
              placeholder="Enter password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
          </div>

          <button type="submit" className="login-button">
            <FaSignInAlt className="btn-icon" />
            Login
          </button>

          {error && <div className="error">{error}</div>}
        </form>
      </div>
    </div>
  );
};

export default Login;
