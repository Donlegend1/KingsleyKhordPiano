import React, { useState } from "react";
import ReactDOM from "react-dom/client";
const currencySigns = {
    NGN: "₦",
    USD: "$",
    EUR: "€",
};

const plans = [
    {
        id: 1,
        type: "monthly",
        tier: "standard",
        amount: { EUR: 23, USD: 26, NGN: 38000 },
        img: "/icons/icon.png",
        bg: "",
    },
    {
        id: 2,
        type: "monthly",
        tier: "premium",
        amount: { EUR: 40, USD: 45, NGN: 70000 },
        img: "/icons/price2.png",
        bg: "/images/Background.jpg",
    },
    {
        id: 3,
        type: "yearly",
        tier: "standard",
        amount: { EUR: 189, USD: 215, NGN: 320000 },
        img: "/icons/icon.png",
        bg: "",
    },
    {
        id: 4,
        type: "yearly",
        tier: "premium",
        amount: { EUR: 369, USD: 420, NGN: 650000 },
        img: "/icons/price2.png",
        bg: "/images/Background.jpg",
    },
];

const PlanSwitchAndCurrencySelect = () => {
    const [selectedPlan, setSelectedPlan] = useState("monthly");
    const [currency, setCurrency] = useState("EUR");
    const [modalOpen, setModalOpen] = useState(false);
    const [selectedPlanDetails, setSelectedPlanDetails] = useState(null);
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const handlePlanToggle = (plan) => {
        setSelectedPlan(plan);
    };

    const authUser = window.authUser || null;

    const handleModalOpen = (plan) => {
        if (!authUser) {
            window.location.href = "/register";
            return;
        }
        setSelectedPlanDetails(plan);
        setModalOpen(true);
    };

    const filteredPlans = plans.filter((plan) => plan.type === selectedPlan);

    return (
        <section className="container mx-auto px-5 md:px-28 bg-cover bg-white mb-20">
            <div className="text-center my-12 mx-auto">
                <p className="font-bold text-4xl">Gain Immediate Entry to </p>
                <p className="font-bold text-4xl my-3 text-[#BC1414]">
                    Kingsley Khord Piano Academy
                </p>
                <p className="text-sm text-gray-500 mx-auto max-w-lg">
                    This structured hands-on piano training was created due to
                    high demand and a lack of available in-depth resources and
                    guidance.
                </p>
            </div>

            <div className="flex flex-col md:flex-row justify-between max-w-3xl mx-auto mb-8">
                <div className="flex items-center justify-center">
                    <div className="relative flex bg-gray-100 rounded-full p-1 shadow-inner">
                        {/* Toggle Indicator */}
                        <div
                            className={`absolute top-1 bottom-1 w-1/2 bg-white rounded-full shadow-md transition-all duration-300 ${
                                selectedPlan === "monthly"
                                    ? "left-1"
                                    : "left-1/2"
                            }`}
                        ></div>

                        {/* Monthly Button */}
                        <button
                            className={`relative z-10 w-32 text-sm font-medium py-2 mx-2 rounded-full transition-colors duration-300 ${
                                selectedPlan === "monthly"
                                    ? "text-gray-800"
                                    : "text-gray-500 hover:text-gray-700"
                            }`}
                            onClick={() => handlePlanToggle("monthly")}
                        >
                            Monthly Plan
                        </button>

                        {/* Yearly Button */}
                        <button
                            className={`relative z-10 w-32 text-sm font-medium py-2 mx-2 rounded-full transition-colors duration-300 ${
                                selectedPlan === "yearly"
                                    ? "text-gray-800"
                                    : "text-gray-500 hover:text-gray-700"
                            }`}
                            onClick={() => handlePlanToggle("yearly")}
                        >
                            <span className="text-sm">Yearly Plan</span>
                            <span className="ml-1 text-red-600 text-xs font-semibold">
                                Save 30%
                            </span>
                        </button>
                    </div>
                </div>

                <div className="relative mt-3">
                    <select
                        value={currency}
                        onChange={(e) => setCurrency(e.target.value)}
                        className="appearance-none px-4 py-2 border rounded-full focus:ring focus:ring-blue-300 transition text-xs"
                    >
                        <option value="EUR">Euro €</option>
                        <option value="USD">USD $</option>
                        <option value="NGN">Naira ₦</option>
                    </select>
                </div>
            </div>

            <div className="flex flex-col sm:flex-row sm:flex-wrap justify-center gap-6">
                {filteredPlans &&
                    filteredPlans.map((plan) => (
                        <div
                            key={plan.id}
                            className="bg-white border border-[#C2D3DD73] rounded-xl shadow-lg p-6 w-full sm:w-[48%] lg:w-[32%]"
                            style={{ backgroundImage: `url(${plan.bg})` }}
                        >
                            <img
                                src={plan.img}
                                alt=""
                                className="mb-4 p-3 border h-16 rounded-3xl"
                            />
                            <h3 className="text-xl font-semibold text-black mb-4">
                                {plan.tier} Plan
                            </h3>
                            <p className="text-2xl font-bold mb-2">
                                {currencySigns[currency]}
                                {plan.amount[currency].toLocaleString()}
                            </p>
                            <p className="text-sm border border-gray-100 my-4"></p>

                            {plan.tier == "standard" && (
                                <ul className="text-sm text-gray-700 mb-6 list-disc list-inside">
                                    <li>Roadmap for all skill levels</li>
                                    <li>Premium midi files</li>
                                    <li>Ear Training Quiz</li>
                                    <li>Practice Routine</li>
                                    <li>Supportive Community</li>
                                </ul>
                            )}

                            {plan.tier == "premium" && (
                                <ul className="text-sm text-gray-700 mb-6 list-disc list-inside">
                                    <li className="text-red-500 font-sf font-semibold">
                                        Everything in the standard plan
                                    </li>
                                    <li>Personalized roadmap course</li>
                                    <li>Weekly live sessions</li>
                                    <li>Structured Accountability plan</li>
                                    <li>In-Depth Master classes</li>
                                </ul>
                            )}

                            <div className="flex justify-center">
                                <button
                                    className={`w-full px-4 py-2 rounded-lg transition ${
                                        plan.tier === "premium"
                                            ? "bg-black text-white hover:bg-gray-700"
                                            : "bg-white text-black border border-[#C2D3DD73] hover:bg-gray-100"
                                    }`}
                                    onClick={() => handleModalOpen(plan)}
                                >
                                    Join Today
                                </button>
                            </div>
                        </div>
                    ))}
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
                            {selectedPlanDetails.amount[
                                currency
                            ].toLocaleString()}{" "}
                            for {selectedPlanDetails.tier} Plan
                        </p>

                        <div className="flex flex-col gap-6">
                            
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
                            <form action="/paystack" method="POST">
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
                            </form>

                            {/* <form action="/paypal/create-order" method="POST">
                                <input type="hidden" name="_token" value={csrfToken} />
                                <input type="hidden" name="plan_id" value={selectedPlanDetails.id} />
                                <input type="hidden" name="tier" value={selectedPlanDetails.tier} />
                                <input type="hidden" name="duration" value={selectedPlanDetails.type} />
                                <input type="hidden" name="currency" value={currency} />
                                <button
                                    type="submit"
                                    className="bg-cyan-500 hover:bg-cyan-800 py-3 rounded text-center font-semibold w-full"
                                >
                                    Pay with PayPal
                                </button>
                            </form> */}
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
                                <span className="inline-block mx-2 bg-gray-300 rounded-md p-1">
                                    <img
                                        src="/icons/paystack2.png"
                                        alt="Paystack"
                                        className="h-4"
                                    />
                                </span>
                                {/* <span className="inline-block mx-2 bg-gray-300 rounded-md p-1">
                                    <img
                                        src="/icons/paypal.png"
                                        alt="Paystack"
                                        className="h-4"
                                    />
                                </span> */}
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
        </React.StrictMode>
    );
}
