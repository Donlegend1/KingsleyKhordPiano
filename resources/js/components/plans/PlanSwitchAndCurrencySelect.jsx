import React, { useState, useEffect } from "react";
import ReactDOM from "react-dom/client";
import { Check, Gem, Target } from "lucide-react";
const currencySigns = {
    NGN: "₦",
    USD: "$",
    EUR: "€",
};


const PlanSwitchAndCurrencySelect = () => {
    const [selectedPlan, setSelectedPlan] = useState("monthly");
    const [currency, setCurrency] = useState("EUR");
    const [modalOpen, setModalOpen] = useState(false);
    const [plans, setPlans] = useState([]);
    const [selectedPlanDetails, setSelectedPlanDetails] = useState(null);
    const [isCheckoutOnly, setIsCheckoutOnly] = useState(false);
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const handlePlanToggle = (plan) => {
        setSelectedPlan(plan);
    };

    const fetchPlans = async () => {
        try {
            const response = await axios.get("/api/plans");
            setPlans(response.data);
        } catch (error) {
            console.error("Failed to fetch plans", error);
        }
    };

    useEffect(() => {
        fetchPlans();
    }, []);

    useEffect(() => {
        const params = new URLSearchParams(window.location.search);
        const planId = params.get("plan_id");
        const curr = params.get("currency");
        const dur = params.get("duration");

        if (curr) {
            setCurrency(curr);
        }
        if (dur) {
            setSelectedPlan(dur);
        }

        if (planId && plans.length > 0) {
            const foundPlan = plans.find((p) => String(p.id) === String(planId));
            if (foundPlan) {
                setSelectedPlanDetails(foundPlan);
                setModalOpen(true);
                setIsCheckoutOnly(true);
                // Clear URL params so reload doesn't trigger modal again
                const newUrl = window.location.pathname;
                window.history.replaceState({}, document.title, newUrl);
            }
        }
    }, [plans]);

    const authUser = window.authUser || null;
    const isSubscribed = window.isSubscribed || false;

    const handleModalOpen = (plan) => {
        setSelectedPlanDetails(plan);
        setModalOpen(true);
    };

    const handleGuestCheckoutChoice = (method) => {
        window.location.href = `/register?plan_id=${selectedPlanDetails.id}&currency=${currency}&tier=${selectedPlanDetails.tier}&duration=${selectedPlanDetails.type}&payment_method=${method}`;
    };

    const filteredPlans = plans.filter((plan) => plan.type === selectedPlan);
    const matchAmountToCurrency = (plan) => {
        switch (currency) {
            case "NGN":
                return Number(plan.price_ngn);

            case "USD":
                return Number(plan.price_usd);

            case "EUR":
                return Number(plan.price_eur);

            default:
                return 0;
        }
    };

    const standardFeatures = [
        "Roadmap for all skill levels",
        "Ear Training Quiz",
        "Songs and midi transcriptions",
        "Downloadable resources",
        "Monthly live sessions",
        "Supportive community",
    ];

    const premiumFeatures = [
        "Everything in the standard plan",
        "Personalized practice plan",
        "Feedback on your playing",
        "Accountability framework",
        "1-on-1 piano coaching",
        "In-depth master classes",
    ];

    const saveBadge = {
        monthly: null,
        quarterly: "Save 10%",
        yearly: "Save 30%",
    };
    if (isCheckoutOnly && selectedPlanDetails) {
        return (
            <div className="flex items-center justify-center py-6 px-4">
                <div className="bg-[#2D2D2D] rounded-2xl shadow-2xl p-8 sm:p-12 w-full max-w-xl border border-gray-800">
                    <h2 className="text-3xl font-extrabold text-center text-white mb-2 tracking-tight">
                        Complete Your Subscription
                    </h2>
                    <div className="bg-[#3D3D3D] rounded-xl p-6 text-center my-6 border border-gray-700">
                        <p className="text-gray-400 text-sm font-semibold uppercase tracking-wider mb-1">
                            Selected Plan
                        </p>
                        <h3 className="text-2xl font-bold text-white mb-2">
                            {selectedPlanDetails.tier?.toLowerCase().includes("premium") ? "Premium" : "Standard"} Plan
                        </h3>
                        <div className="text-3xl font-black text-white">
                            {currencySigns[currency]}
                            {matchAmountToCurrency(selectedPlanDetails)}
                            <span className="text-sm text-gray-400 font-medium font-normal">
                                /{selectedPlanDetails.type === "monthly" ? "month" : selectedPlanDetails.type === "quarterly" ? "quarter" : "year"}
                            </span>
                        </div>
                    </div>

                    <p className="text-center text-gray-300 mb-6 font-medium">
                        Choose your preferred payment method below to complete payment:
                    </p>

                    <div className="flex flex-col gap-4">
                        <form action="/stripe/create" method="POST">
                            <input type="hidden" name="_token" value={csrfToken} />
                            <input type="hidden" name="plan_id" value={selectedPlanDetails.id} />
                            <input type="hidden" name="tier" value={selectedPlanDetails.tier} />
                            <input type="hidden" name="duration" value={selectedPlanDetails.type} />
                            <input type="hidden" name="currency" value={currency} />
                            <button
                                type="submit"
                                className="bg-[#FFD736] hover:bg-[#E5C130] text-gray-900 py-3.5 rounded-xl text-center font-bold w-full transition duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                            >
                                Pay with Stripe
                            </button>
                        </form>

                        {/* <form action="/paystack" method="POST">
                            <input type="hidden" name="_token" value={csrfToken} />
                            <input type="hidden" name="plan_id" value={selectedPlanDetails.id} />
                            <input type="hidden" name="tier" value={selectedPlanDetails.tier} />
                            <input type="hidden" name="duration" value={selectedPlanDetails.type} />
                            <input type="hidden" name="currency" value={currency} />
                            <button
                                type="submit"
                                className="bg-white hover:bg-gray-100 text-gray-900 py-3.5 rounded-xl text-center font-bold w-full transition duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                            >
                                Pay with Paystack
                            </button>
                        </form> */}

                        {currency !== "NGN" && (
                            <form action="/paypal/create-order" method="POST">
                                <input type="hidden" name="_token" value={csrfToken} />
                                <input type="hidden" name="plan_id" value={selectedPlanDetails.id} />
                                <input type="hidden" name="tier" value={selectedPlanDetails.tier} />
                                <input type="hidden" name="duration" value={selectedPlanDetails.type} />
                                <input type="hidden" name="currency" value={currency} />
                                <button
                                    type="submit"
                                    className="bg-[#003087] hover:bg-[#001C54] text-white py-3.5 rounded-xl text-center font-bold w-full transition duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                                >
                                    Pay with PayPal
                                </button>
                            </form>
                        )}
                    </div>

                    <div className="mt-8 flex items-center justify-center text-xs text-gray-500">
                        <p className="flex items-center gap-2">
                            Secure payment powered by
                            <span className="inline-block bg-gray-800 rounded px-1.5 py-0.5">
                                <img src="/icons/stripe2.png" alt="Stripe" className="h-3" />
                            </span>
                            {/* <span className="inline-block bg-gray-800 rounded px-1.5 py-0.5">
                                <img src="/icons/paystack2.png" alt="Paystack" className="h-3" />
                            </span> */}
                            {currency !== "NGN" && (
                                <span className="inline-block bg-gray-800 rounded px-1.5 py-0.5">
                                    <img src="/icons/paypal.png" alt="PayPal" className="h-3" />
                                </span>
                            )}
                        </p>
                    </div>

                    <div className="mt-8 text-center">
                        <button
                            type="button"
                            onClick={() => {
                                setIsCheckoutOnly(false);
                                setModalOpen(false);
                            }}
                            className="text-gray-400 hover:text-white text-sm font-semibold transition"
                        >
                            &larr; Choose a different plan
                        </button>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <section className="container mx-auto px-5 md:px-10 lg:px-20 bg-white mb-24 overflow-x-hidden">
            <div className="text-center pt-4 pb-10 mx-auto max-w-3xl">
                <span className="inline-block text-[11px] font-bold uppercase tracking-[0.2em] text-[#BC1414] mb-3">
                    Membership
                </span>
                <h2 className="font-extrabold text-2xl sm:text-3xl lg:text-4xl text-gray-900 leading-tight">
                    Gain Immediate Entry to{" "}
                    <span className="text-[#BC1414] block sm:inline sm:whitespace-nowrap">
                        Kingsley Khord Piano Academy
                    </span>
                </h2>
            </div>

            <div className="flex flex-col sm:flex-row items-center justify-center gap-3 mb-12">
                <div className="flex items-center gap-1 bg-gray-100 rounded-full p-1">
                    {["monthly", "quarterly", "yearly"].map((cycle) => (
                        <button
                            key={cycle}
                            onClick={() => handlePlanToggle(cycle)}
                            className={`flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-1.5 px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 min-h-[60px] sm:min-h-0 ${
                                selectedPlan === cycle
                                    ? "bg-white text-gray-900 shadow-sm"
                                    : "text-gray-500 hover:text-gray-700"
                            }`}
                        >
                            <span className="capitalize whitespace-nowrap">{cycle}</span>
                            {saveBadge[cycle] && (
                                <span className="text-[10px] font-bold text-green-600 bg-green-50 px-1.5 py-0.5 rounded-full whitespace-nowrap">
                                    {saveBadge[cycle]}
                                </span>
                            )}
                        </button>
                    ))}
                </div>

                <div className="relative ml-auto sm:ml-0">
                    <select
                        value={currency}
                        onChange={(e) => setCurrency(e.target.value)}
                        className="no-native-arrow bg-gray-900 pl-4 pr-9 py-2.5 border border-gray-900 rounded-full text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 transition cursor-pointer"
                    >
                        <option value="EUR">Euro €</option>
                        <option value="USD">USD $</option>
                        <option value="NGN">Naira ₦</option>
                    </select>
                    <span className="absolute inset-y-0 right-3.5 flex items-center pointer-events-none">
                        <svg className="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </span>
                </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-3xl mx-auto">
                {filteredPlans &&
                    filteredPlans.map((plan) => {
                        const isPremium = plan.tier?.toLowerCase().includes("premium");
                        return (
                            <div
                                key={plan.id}
                                className={`relative rounded-2xl p-8 flex flex-col transition-shadow ${
                                    isPremium
                                        ? "border-2 border-gray-900 shadow-md bg-cover bg-center bg-white"
                                        : "border border-gray-300 shadow-sm hover:shadow-md bg-gray-50"
                                }`}
                                style={
                                    isPremium && plan.background
                                        ? { backgroundImage: `url(${plan.background})` }
                                        : undefined
                                }
                            >
                                {isPremium && (
                                    <span className="absolute -top-3 right-8 bg-gray-900 text-white text-[11px] font-bold uppercase tracking-wide px-3 py-1 rounded-full">
                                        Best Value
                                    </span>
                                )}

                                <div
                                    className={`w-12 h-12 rounded-xl flex items-center justify-center overflow-hidden mb-5 ${
                                        isPremium
                                            ? "bg-red-50 border border-red-100"
                                            : "bg-gray-100 border border-gray-200"
                                    }`}
                                >
                                    {isPremium ? (
                                        <Gem className="w-6 h-6 text-red-500" />
                                    ) : (
                                        <Target className="w-6 h-6 text-indigo-500" />
                                    )}
                                </div>

                                <h3 className="text-lg font-bold text-gray-900">
                                    {plan.tier?.toLowerCase().includes("premium") ? "Premium" : "Standard"} Plan
                                </h3>

                                <div className="flex items-baseline gap-1.5 mt-3 mb-1">
                                    <span className="text-4xl font-extrabold text-gray-900">
                                        {currencySigns[currency]}
                                        {matchAmountToCurrency(plan).toLocaleString()}
                                    </span>
                                    <span className="text-sm text-gray-400 font-medium">
                                        /{selectedPlan === "monthly" ? "mo" : selectedPlan === "quarterly" ? "qtr" : "yr"}
                                    </span>
                                </div>

                                <div className="border-t border-gray-100 my-6"></div>

                                <ul className="space-y-3 mb-8 flex-1">
                                    {(isPremium ? premiumFeatures : standardFeatures).map(
                                        (feature, idx) => (
                                            <li
                                                key={idx}
                                                className="flex items-start gap-2.5 text-base text-black"
                                            >
                                                <Check className="w-4 h-4 text-indigo-500 flex-shrink-0 mt-0.5" />
                                                <span className={idx === 0 && isPremium ? "font-semibold text-red-500" : ""}>
                                                    {feature}
                                                </span>
                                            </li>
                                        ),
                                    )}
                                </ul>

                                <button
                                    className={`w-full py-3 rounded-xl text-sm font-bold transition ${
                                        isSubscribed
                                            ? "bg-gray-200 text-gray-400 cursor-not-allowed"
                                            : "bg-gray-900 text-white hover:bg-gray-800"
                                    }`}
                                    onClick={() => !isSubscribed && handleModalOpen(plan)}
                                    disabled={isSubscribed}
                                >
                                    {isSubscribed ? "Already Subscribed" : "Join Now"}
                                </button>
                            </div>
                        );
                    })}
            </div>

            {modalOpen && selectedPlanDetails && (
                <div className="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 py-9">
                    <button
                        className="absolute top-4 right-4 text-white text-lg"
                        onClick={() => setModalOpen(false)}
                    >
                        X
                    </button>
                    <div className="bg-[#2D2D2D] rounded-xl shadow-xl p-12 w-full max-w-lg mx-auto">
                        <h2 className="text-2xl font-bold text-center text-white mb-2">
                            Choose your Preferred Payment Option
                        </h2>
                        <p className="text-center text-white mb-4">
                            {currencySigns[currency]}
                            {matchAmountToCurrency(selectedPlanDetails)}
                             {" for "}{selectedPlanDetails.tier?.toLowerCase().includes("premium") ? "Premium" : "Standard"} Plan
                        </p>

                        <div className="flex flex-col gap-6">
                            {authUser ? (
                                <>
                                    <form action="/stripe/create" method="POST">
                                        <input
                                            type="hidden"
                                            name="_token"
                                            value={csrfToken}
                                        />
                                        <input
                                            type="hidden"
                                            name="plan_id"
                                            value={selectedPlanDetails.id}
                                        />
                                        <input
                                            type="hidden"
                                            name="tier"
                                            value={selectedPlanDetails.tier}
                                        />
                                        <input
                                            type="hidden"
                                            name="duration"
                                            value={selectedPlanDetails.type}
                                        />
                                        <input
                                            type="hidden"
                                            name="currency"
                                            value={currency}
                                        />
                                        <button
                                            type="submit"
                                            className="bg-[#FFD736] hover:bg-[#a7923e] py-3 rounded text-center font-semibold w-full"
                                        >
                                            Pay with Stripe
                                        </button>
                                    </form>
                                    {/* <form action="/paystack" method="POST">
                                        <input
                                            type="hidden"
                                            name="_token"
                                            value={csrfToken}
                                        />
                                        <input
                                            type="hidden"
                                            name="plan_id"
                                            value={selectedPlanDetails.id}
                                        />
                                        <input
                                            type="hidden"
                                            name="tier"
                                            value={selectedPlanDetails.tier}
                                        />
                                        <input
                                            type="hidden"
                                            name="duration"
                                            value={selectedPlanDetails.type}
                                        />
                                        <input
                                            type="hidden"
                                            name="currency"
                                            value={currency}
                                        />
                                        <button
                                            type="submit"
                                            className="bg-[#FAFAFA] hover:bg-[#e7dfdf] py-3 rounded text-center font-semibold w-full"
                                        >
                                            Pay with Paystack
                                        </button>
                                    </form> */}
                                    {currency !== "NGN" && (
                                        <form action="/paypal/create-order" method="POST">
                                            <input type="hidden" name="_token" value={csrfToken} />
                                            <input type="hidden" name="plan_id" value={selectedPlanDetails.id} />
                                            <input type="hidden" name="tier" value={selectedPlanDetails.tier} />
                                            <input type="hidden" name="duration" value={selectedPlanDetails.type} />
                                            <input type="hidden" name="currency" value={currency} />
                                            <button
                                                type="submit"
                                                className="bg-[#003087] hover:bg-[#001C54] text-white py-3 rounded text-center font-semibold w-full transition duration-200"
                                            >
                                                Pay with PayPal
                                            </button>
                                        </form>
                                    )}
                                </>
                            ) : (
                                <>
                                    <button
                                        type="button"
                                        onClick={() => handleGuestCheckoutChoice("stripe")}
                                        className="bg-[#FFD736] hover:bg-[#a7923e] py-3 rounded text-center font-semibold w-full text-gray-900"
                                    >
                                        Pay with Stripe
                                    </button>
                                    {/* <button
                                        type="button"
                                        onClick={() => handleGuestCheckoutChoice("paystack")}
                                        className="bg-[#FAFAFA] hover:bg-[#e7dfdf] py-3 rounded text-center font-semibold w-full text-gray-900"
                                    >
                                        Pay with Paystack
                                    </button> */}
                                    {currency !== "NGN" && (
                                        <button
                                            type="button"
                                            onClick={() => handleGuestCheckoutChoice("paypal")}
                                            className="bg-[#003087] hover:bg-[#001C54] text-white py-3 rounded text-center font-semibold w-full transition duration-200"
                                        >
                                            Pay with PayPal
                                        </button>
                                    )}
                                </>
                            )}
                        </div>

                        <div className="mt-4 flex items-center justify-center text-sm text-gray-400">
                            <p>
                                Powered by
                                <span className="inline-block mx-2 bg-gray-300 rounded-md p-1">
                                    <img
                                        src="/icons/stripe2.png"
                                        alt="Stripe"
                                        className="h-4"
                                    />
                                </span>
                                {/* <span className="inline-block mx-2 bg-gray-300 rounded-md p-1">
                                    <img
                                        src="/icons/paystack2.png"
                                        alt="Paystack"
                                        className="h-4"
                                    />
                                </span> */}
                                {currency !== "NGN" && (
                                    <span className="inline-block mx-2 bg-gray-300 rounded-md p-1">
                                        <img
                                            src="/icons/paypal.png"
                                            alt="PayPal"
                                            className="h-4"
                                        />
                                    </span>
                                )}
                            </p>
                        </div>
                    </div>
                </div>
            )}
        </section>
    );
};

export default PlanSwitchAndCurrencySelect;

if (document.getElementById("plan-switch")) {
    const Index = ReactDOM.createRoot(document.getElementById("plan-switch"));

    Index.render(
        <React.StrictMode>
            <PlanSwitchAndCurrencySelect />
        </React.StrictMode>,
    );
}
