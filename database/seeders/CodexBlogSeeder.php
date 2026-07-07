<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;

class CodexBlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $blogs = [
            [
                'title' => 'Options Greeks & Volatility Surfaces',
                'slug' => 'options-greeks-volatility-surfaces',
                'category' => 'Course',
                'image' => 'marketing/codex_options.png',
                'author' => 'P2B Trading Academy',
                'body' => '<p class="lead">Understanding option pricing derivatives and the structural geometry of volatility surfaces is the hallmark of institutional-grade risk management. This comprehensive course covers the primary Greeks and the dynamics of the volatility surface.</p>

<h2>Section 1: The Core Option Greeks</h2>
<p>Option Greeks measure the sensitivity of an option\'s price to various market parameters: underlying asset price, time decay, volatility, and interest rates.</p>

<ul>
    <li><strong>Delta (&Delta;):</strong> Measures the rate of change of the option price with respect to a change in the underlying asset\'s price. For a call option, Delta ranges from 0 to 1, while for a put option, it ranges from -1 to 0.</li>
    <li><strong>Gamma (&Gamma;):</strong> Measures the rate of change in Delta with respect to changes in the underlying price. Gamma is highest for at-the-money (ATM) options and decreases as the option moves deep in-the-money (ITM) or out-of-the-money (OTM).</li>
    <li><strong>Theta (&Theta;):</strong> Represents the sensitivity of the option price to the passage of time (commonly referred to as time decay). Theta is almost always negative for long options positions, accelerating as expiration approaches.</li>
    <li><strong>Vega (&nu;):</strong> Measures the sensitivity of the option price to changes in the implied volatility of the underlying asset. Vega is highest for longer-dated ATM options.</li>
</ul>

<blockquote>
    <p>Mathematical pricing models, such as the Black-Scholes-Merton (BSM) formulation, solve for the theoretical value of European options by establishing a risk-free replication portfolio. The partial differential equation (PDE) is defined as:</p>
    <div style="font-family: var(--mono); font-size: 15px; margin: 12px 0; color: var(--emerald); text-align: center;">
        &part;V/&part;t + &frac12;&sigma;&sup2;S&sup2;&part;&sup2;V/&part;S&sup2; + rS&part;V/&part;S - rV = 0
    </div>
</blockquote>

<h2>Section 2: Volatility Skew and the Volatility Smile</h2>
<p>The Black-Scholes model assumes that the volatility of the underlying asset is constant across all strike prices and expiries. However, real-world market observations reveal that implied volatility varies depending on the strike price. This variation produces the characteristic "volatility smile" or "volatility skew".</p>
<p>For equity options, the curve typically slopes downwards to the right (skew), reflecting higher implied volatility for low strike puts. This skew is driven by crash protection demands (out-of-the-money puts are highly sought after by institutional managers for tail risk hedging).</p>

<h2>Section 3: The 3D Volatility Surface</h2>
<p>When we plot implied volatility across both strike price (or moneyness) and time-to-expiration, we generate a 3D visualization known as the <strong>Volatility Surface</strong>. This surface is critical for pricing exotic options and managing multi-asset portfolios.</p>
<p>By understanding how the surface deforms under market stress, quantitative traders can identify mispriced spreads and exploit arbitrage opportunities between different points on the curve.</p>',
            ],
            [
                'title' => 'Macroeconomic Regime Changes in Q3',
                'slug' => 'macroeconomic-regime-changes-in-q3',
                'category' => 'Research',
                'image' => 'marketing/codex_macro.png',
                'author' => 'P2B Research Desk',
                'body' => '<p class="lead">As we transition into the second half of the year, macroeconomic indicators point to a structural shift in global market regimes. This institutional research report analyzes key macroeconomic indicators and maps out portfolio allocation strategies for the third quarter.</p>

<h2>Section 1: Inflation Deceleration and Central Bank Pivots</h2>
<p>After a prolonged period of aggressive monetary tightening, global inflation rates are approaching target levels. Consequently, major central banks—including the Federal Reserve and the European Central Bank—are shifting towards a rate-cutting cycle.</p>
<p>This pivot marks a transition from a high-interest-rate regime to a moderate-rate expansionary phase, altering the discount factors applied to growth equities and long-duration debt assets.</p>

<blockquote>
    <p>"The macroeconomic outlook suggests a soft landing, but persistent service-sector inflation and supply chain restructuring present upside risks to core CPI, which could limit the depth of central bank cutting cycles."</p>
</blockquote>

<h2>Section 2: Asset Allocation Performance Matrix</h2>
<p>Different asset classes perform uniquely depending on the dominant macroeconomic regime. The table below outlines asset class preferences based on inflation and growth dynamics:</p>

<div class="table-responsive" style="margin: 24px 0; border: 1px solid var(--border); border-radius: 6px; overflow: hidden;">
    <table class="table" style="width: 100%; margin-bottom: 0; color: var(--t2); background: rgba(5,5,5,0.4); border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border); background: rgba(255,255,255,0.02);">
                <th style="padding: 12px 16px; text-align: left; font-family: var(--mono); font-size: 11px; text-transform: uppercase; color: var(--gold); border: none;">Regime</th>
                <th style="padding: 12px 16px; text-align: left; font-family: var(--mono); font-size: 11px; text-transform: uppercase; color: var(--gold); border: none;">Growth Trend</th>
                <th style="padding: 12px 16px; text-align: left; font-family: var(--mono); font-size: 11px; text-transform: uppercase; color: var(--gold); border: none;">Inflation Trend</th>
                <th style="padding: 12px 16px; text-align: left; font-family: var(--mono); font-size: 11px; text-transform: uppercase; color: var(--gold); border: none;">Favored Assets</th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 12px 16px; font-weight: 500; border: none;">Goldilocks</td>
                <td style="padding: 12px 16px; border: none;">Rising</td>
                <td style="padding: 12px 16px; border: none;">Falling / Stable</td>
                <td style="padding: 12px 16px; color: var(--emerald); border: none;">Equities, Tech, Real Estate</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 12px 16px; font-weight: 500; border: none;">Reflation</td>
                <td style="padding: 12px 16px; border: none;">Rising</td>
                <td style="padding: 12px 16px; border: none;">Rising</td>
                <td style="padding: 12px 16px; color: var(--emerald); border: none;">Commodities, Cyclicals, Value</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 12px 16px; font-weight: 500; border: none;">Stagflation</td>
                <td style="padding: 12px 16px; border: none;">Falling</td>
                <td style="padding: 12px 16px; border: none;">Rising</td>
                <td style="padding: 12px 16px; color: var(--emerald); border: none;">Gold, Energy, Cash</td>
            </tr>
            <tr>
                <td style="padding: 12px 16px; font-weight: 500; border: none;">Deflationary</td>
                <td style="padding: 12px 16px; border: none;">Falling</td>
                <td style="padding: 12px 16px; border: none;">Falling</td>
                <td style="padding: 12px 16px; color: var(--emerald); border: none;">Bonds, Defensive Equities, USD</td>
            </tr>
        </tbody>
    </table>
</div>

<h2>Section 3: Strategic Portfolio Adjustments for Q3</h2>
<p>Given the high probability of a "Goldilocks to Reflation" transition, we recommend institutional portfolios execute a measured rotation:</p>
<ul>
    <li>Increase exposure to high-grade sovereign debt to lock in yields before rate cuts progress.</li>
    <li>Selectively add to cyclical sectors that benefit from persistent nominal growth.</li>
    <li>Maintain a core allocation to gold and digital assets as structural hedges against currency debasement and geopolitical volatility.</li>
</ul>',
            ],
            [
                'title' => 'Building Alpha-Generating Algos',
                'slug' => 'building-alpha-generating-algos',
                'category' => 'Webinar',
                'image' => 'marketing/codex_algo.png',
                'author' => 'P2B Quantitative Research',
                'body' => '<p class="lead">Systematic trading eliminates emotional biases and permits rigorous historical backtesting. This technical webinar write-up explores the foundational principles of building alpha-generating quantitative algorithms.</p>

<h2>Section 1: The Quantitative Research Pipeline</h2>
<p>Developing an algorithmic trading strategy follows a structured pipeline: hypothesis formulation, data acquisition & cleaning, backtesting, execution modeling, and portfolio risk management.</p>
<p>The transition from a theoretical signal to a live trading strategy requires accounting for transaction costs, market impact, slippage, and latency.</p>

<h2>Section 2: Python Signal Generator Implementation</h2>
<p>Below is a production-ready Python example demonstrating a simple dual moving average crossover strategy using pandas. This formulates the core signal generation layer:</p>

<pre><code>import pandas as pd
import numpy as np

def generate_signals(df, short_window=20, long_window=50):
    signals = pd.DataFrame(index=df.index)
    signals[\'price\'] = df[\'close\']
    
    # Calculate Simple Moving Averages
    signals[\'short_mavg\'] = df[\'close\'].rolling(window=short_window, min_periods=1).mean()
    signals[\'long_mavg\'] = df[\'close\'].rolling(window=long_window, min_periods=1).mean()
    
    # Create signals
    signals[\'signal\'] = 0.0
    signals[\'signal\'][short_window:] = np.where(
        signals[\'short_mavg\'][short_window:] > signals[\'long_mavg\'][short_window:], 1.0, 0.0
    )   
    
    # Generate trading orders
    signals[\'positions\'] = signals[\'signal\'].diff()
    return signals</code></pre>

<h2>Section 3: Performance Metrics & Overfitting Prevention</h2>
<p>When evaluating a trading strategy, simple profitability is an insufficient metric. A quantitative analyst must evaluate risk-adjusted metrics:</p>
<ul>
    <li><strong>Sharpe Ratio:</strong> Measures the excess return per unit of volatility. A Sharpe ratio greater than 2.0 on daily data is generally considered institutional quality.</li>
    <li><strong>Max Drawdown:</strong> The largest peak-to-trough decline in portfolio value. Crucial for calculating capital requirements and margin limits.</li>
    <li><strong>Sortino Ratio:</strong> Measures return relative to downside volatility, ignoring positive price swings.</li>
</ul>
<p>To prevent overfitting (curve-fitting), use Out-of-Sample (OOS) testing, cross-validation, and avoid parameter optimization based solely on historical peak returns.</p>',
            ],
        ];

        foreach ($blogs as $b) {
            Blog::updateOrCreate(
                ['slug' => $b['slug']],
                [
                    'title' => $b['title'],
                    'category' => $b['category'],
                    'image' => $b['image'],
                    'author' => $b['author'],
                    'body' => $b['body'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
