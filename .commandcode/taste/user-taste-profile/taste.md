# User Taste Profile
- Prefers Vietnamese language for communication. Confidence: 0.95
- Uses `/code-review` command to trigger code review workflow. Confidence: 0.9
- Code review output written to `review.md` file in the plugin directory. Confidence: 0.85
- Runs `phpcs` with standard at `../meta-box-aio/phpcs.xml` (also available as `composer run phpcs`). Confidence: 0.95
- Maintains PHP 7.4 backward compatibility — evaluates PHP functions in **WordPress context**, not raw PHP. WP core provides polyfills (e.g. `str_contains()` via `wp-includes/compat.php`), so using these functions is acceptable and NOT a bug. Confidence: 0.95
- Project context: WordPress plugin (mb-elementor-integrator) integrating Meta Box fields with Elementor widgets. Confidence: 0.8
- Expects reviews to cover: logic bugs, security (XSS/SQLi/escaping), code standards (phpcs), performance, and PHP/WP version compatibility. Confidence: 0.8
- Expects re-evaluation even after small incremental changes — asks "có ảnh hưởng gì ko? hoặc có gì bị sai không?" for minor tweaks, not just major updates. Iterative review cycle. Confidence: 0.85
- Prefers concise, readable boolean expressions over verbose comparisons: `! is_int(...)` instead of `false === is_int(...)`, `count(...)` instead of `0 < count(...)`, `! str_contains(...)` instead of `false === strpos(...)`. Proactively cleans up such patterns across the codebase. Confidence: 0.9
