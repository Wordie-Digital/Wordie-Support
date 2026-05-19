# Member Login Blocked

**Category**: WordPress / Security  
**Confidence**: 8/10  
**Pattern frequency**: High  
**Last seen**: 2026-05-19  

## Symptom

Client or their end-user reports being unable to log in to the WordPress site. Symptoms include:
- Blank page after login attempt
- "Too many failed login attempts" message
- Redirect loop on the login page
- Access blocked with a Cloudflare or Wordfence error page

## Root cause

Wordfence (or Cloudflare WAF) has blocked the client's IP address after detecting multiple failed login attempts — either from the client themselves mistyping credentials, or from a brute-force attempt targeting the same IP range.

Wordfence blocks are time-limited but can be permanent if the threshold is exceeded. The block applies at the IP level, meaning all users behind the same office/network IP are affected.

## Diagnosis steps

1. Log into WordPress admin (from an unblocked IP, e.g., Wordie VPN or a different connection).
2. Navigate to: **Wordfence > Firewall > Blocked IPs**.
3. Confirm the client's IP address appears in the blocked list.
   - Ask client to visit `https://whatismyip.com` and send their current IP.
4. Check **Wordfence > Scan > Recent Events** for the failed login attempts to confirm this is the root cause (not a different firewall layer).
5. If the IP doesn't appear in Wordfence, check **Cloudflare > Security > Events** for WAF or Rate Limiting blocks.

## Fix

**Immediate (unblock):**
1. Wordfence > Firewall > Blocked IPs > find the IP > click **Unblock**.
2. If blocked via Cloudflare: Cloudflare dashboard > Security > WAF > IP Access Rules > remove or whitelist the IP.

**Prevent recurrence:**
1. Whitelist the client's office IP in Wordfence: **Wordfence > All Options > Firewall Options > Whitelisted IP Addresses** — add the IP.
2. If the client has a dynamic IP (residential/mobile), whitelist their full IP range or recommend they use a VPN with a fixed IP.
3. Confirm the client's credentials are correct and enable "Remember Me" to reduce login frequency.

## Prevention

- Add client office IPs to Wordfence whitelist at project kick-off as a standard practice.
- Document whitelisted IPs in the HubSpot company record for that client.
- Automate: build a self-serve IP whitelist request form (flagged as automation candidate — 3 client occurrences in 2026).

## Agent script

> "Your IP address has been temporarily blocked by our security system after detecting multiple failed login attempts — this is a protective measure to prevent unauthorised access. We've unblocked it now and you should be able to log in again immediately. To prevent this happening in future, we've also whitelisted your office IP address. If you're working from a different location and get blocked again, reply to this ticket and we'll sort it within the hour."

## Chatbot response

> **Locked out of your WordPress site?**  
> If you're seeing a security block when trying to log in, your IP address may have been flagged by our firewall. This is quick to fix — please [submit a support ticket](https://wordie.com.au/support) with your site URL and your current IP address (visit whatismyip.com to find it). We'll unblock you within the hour.

## Sources

- Wynstan ticket — login blocked, Wordfence IP block confirmed
- Eyecare Plus ticket — login blocked; confirmed via email chain that security plugin was the cause
- Elegance Tiles ticket — same pattern; flagged as AUTOMATION CANDIDATE ⚡ in 2026-05-19 triage
