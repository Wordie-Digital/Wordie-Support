import express from 'express';
import cors from 'cors';
import helmet from 'helmet';
import rateLimit from 'express-rate-limit';
import { sitesRouter } from './routes/sites';
import { scheduleDailySnapshots } from './jobs/daily-snapshot';
import { scheduleMonthlyReports } from './jobs/monthly-report';

const app  = express();
const PORT = process.env.PORT ?? 3031;

// ── Security ──────────────────────────────────────────────────────────────────
app.use(helmet());
app.use(cors({
  origin: process.env.DASHBOARD_URL ?? 'http://localhost:3030',
  methods: ['GET', 'POST'],
}));
app.use(rateLimit({
  windowMs: 60 * 1000,
  max: 120,
  standardHeaders: true,
  legacyHeaders: false,
}));
app.use(express.json());

// ── Routes ────────────────────────────────────────────────────────────────────
app.get('/api/health', (_req, res) => {
  res.json({ status: 'ok', timestamp: new Date().toISOString() });
});

app.use('/api/sites', sitesRouter);

// ── 404 ───────────────────────────────────────────────────────────────────────
app.use((_req, res) => {
  res.status(404).json({ error: 'Not found' });
});

// ── Start ─────────────────────────────────────────────────────────────────────
app.listen(PORT, () => {
  console.log(`[api] Running on port ${PORT}`);

  if (process.env.NODE_ENV === 'production') {
    scheduleDailySnapshots();
    scheduleMonthlyReports();
    console.log('[api] Scheduled jobs active');
  }
});

export default app;
