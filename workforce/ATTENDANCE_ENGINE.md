# Four-Layer Attendance Engine Architecture

## 1. Multi-Layer Verification Flow
Every check-in event evaluates 4 independent signals before confirming attendance:

### Layer 1: Geofence Verification (GPS)
- Browser Geolocation API captures `(latitude, longitude, accuracy)`.
- Haversine formula evaluates distance to configured site coordinates:
  $$d = 2r \arcsin\left(\sqrt{\sin^2\left(\frac{\Delta \phi}{2}\right) + \cos(\phi_1)\cos(\phi_2)\sin^2\left(\frac{\Delta \lambda}{2}\right)}\right)$$
- If distance $\le$ `geofence_radius` (default 75m): **PASS**.
- If distance $>$ radius: **FAIL** (Check-in blocked with descriptive UI notice).
- GPS Spoofing Detection: Flags accuracy $> 100\text{m}$, unrealistic speed jumps, or mismatched timestamps.

### Layer 2: Signed Dynamic QR Code
- Site Terminal / Supervisor Device displays an HMAC-SHA256 signed QR code:
  $$\text{Token} = \text{HMAC-SHA256}(\text{site\_id} + \text{nonce} + \text{expires\_at}, \text{APP\_KEY})$$
- Refreshes every 30 seconds.
- Validated on server for signature authenticity, single-use nonce uniqueness, and non-expiration.

### Layer 3: Live Selfie & Biometric Privacy
- Camera captures live stream frame with timestamp watermark.
- Stored securely in `storage/uploads/selfies/` with restricted access permissions.
- Pluggable `FaceVerificationProvider` abstraction with configurable retention days (`FACE_DATA_RETENTION_DAYS`).

### Layer 4: Verification Risk Score & State Machine
- System calculates `verification_score` (0-100) & `risk_score` (0-100).
- State Transitions:
  - `VERIFIED / CHECKED_IN`: High confidence score ($\ge 85$).
  - `MANUAL_REVIEW`: Moderate score ($50-84$) flagged for supervisor review.
  - `REJECTED`: Outside geofence or expired token ($< 50$).
  - `CHECKED_OUT`: Verified check-out calculation of total work and overtime minutes.
