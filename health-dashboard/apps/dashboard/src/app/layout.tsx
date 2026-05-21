import type { Metadata } from 'next';
import './globals.css';

export const metadata: Metadata = {
  title: 'Wordie — Website Health Dashboard',
  description: 'Monthly maintenance intelligence for your WordPress website',
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en">
      <head>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="anonymous" />
      </head>
      <body className="min-h-screen bg-wordie-off-white">
        <header className="bg-wordie-dark-teal border-b border-wordie-near-black no-print">
          <div className="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <div className="flex items-center gap-3">
              {/* Wordie wordmark */}
              <svg width="80" height="24" viewBox="0 0 80 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Wordie">
                <text x="0" y="20" fontFamily="Montserrat, sans-serif" fontWeight="700" fontSize="20" fill="#F6F9F9" letterSpacing="-0.5">wordie</text>
              </svg>
              <span className="hidden sm:block text-wordie-light-green/60 text-sm font-body">|</span>
              <span className="hidden sm:block text-wordie-light-green/80 text-sm font-body">Health Dashboard</span>
            </div>
            <div className="flex items-center gap-2 text-sm text-wordie-light-green/70 font-body">
              <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
              Live
            </div>
          </div>
        </header>
        <main className="max-w-7xl mx-auto px-4 sm:px-6 py-8">
          {children}
        </main>
        <footer className="mt-16 border-t border-gray-200 no-print">
          <div className="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between text-xs text-gray-400 font-body">
            <span>© {new Date().getFullYear()} Wordie. WordPress, done properly.</span>
            <a href="mailto:support@wordie.com.au" className="hover:text-wordie-accent-teal transition-colors">
              support@wordie.com.au
            </a>
          </div>
        </footer>
      </body>
    </html>
  );
}
