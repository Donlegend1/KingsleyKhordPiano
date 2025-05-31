import React, { useState } from "react";
import ReactDOM from "react-dom/client";
const currencySigns = {
    eur: "€",
    usd: "$",
    naira: "₦",
};

const plans = [
    {
        id: 1,
        type: "monthly",
        tier: "standard",
        amount: { eur: 23, usd: 26, naira: 38000 },
        img: "/icons/icon.png",
        bg: "",
    },
    {
        id: 2,
        type: "monthly",
        tier: "premium",
        amount: { eur: 40, usd: 45, naira: 70000 },
        img: "/icons/price2.png",
        bg: "/images/Background.jpg",
    },
    {
        id: 3,
        type: "yearly",
        tier: "standard",
        amount: { eur: 189, usd: 215, naira: 320000 },
        img: "/icons/icon.png",
        bg: "",
    },
    {
        id: 4,
        type: "yearly",
        tier: "premium",
        amount: { eur: 369, usd: 420, naira: 650000 },
        img: "/icons/price2.png",
        bg: "/images/Background.jpg",
    },
];

const PlanSwitchAndCurrencySelect = () => {
    const [selectedPlan, setSelectedPlan] = useState("monthly");
    const [currency, setCurrency] = useState("eur");
    const [modalOpen, setModalOpen] = useState(false);
    const [selectedPlanDetails, setSelectedPlanDetails] = useState(null);
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const handlePlanToggle = (plan) => {
        setSelectedPlan(plan);
    };

    const handleModalOpen = (plan) => {
        setSelectedPlanDetails(plan);
        setModalOpen(true);
    };

    const filteredPlans = plans.filter((plan) => plan.type === selectedPlan);

    return (
        <section
            className="container mx-auto px-5 md:px-28 h-[100vh] bg-white mb-20"
            id="plans"
        >
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
                <div className="relative flex gap-0">
                    <button
                        className={`px-4 py-2 rounded-full z-10 -mr-8 transition ${
                            selectedPlan === "monthly"
                                ? "bg-gray-300 text-gray-800"
                                : "bg-white text-gray-700 border"
                        }`}
                        onClick={() => handlePlanToggle("monthly")}
                    >
                        Monthly Plan
                    </button>
                    <button
                        className={`px-4 py-2 rounded-full z-0 transition -ml-8 ${
                            selectedPlan === "yearly"
                                ? "bg-gray-300 text-gray-800"
                                : "bg-white text-gray-700 border"
                        }`}
                        onClick={() => handlePlanToggle("yearly")}
                    >
                        <span className="text-gray-700 ml-12">Yearly Plan</span>
                        <span className="text-red-600 mx-2 font-sf text-xs">
                            Save 30%
                        </span>
                    </button>
                </div>

                <div className="relative">
                    <select
                        value={currency}
                        onChange={(e) => setCurrency(e.target.value)}
                        className="appearance-none px-4 py-2 border rounded-full focus:ring focus:ring-blue-300 transition text-xs"
                    >
                        <option value="eur">Euro €</option>
                        <option value="usd">USD $ </option>
                        <option value="naira">Naira ₦ </option>
                    </select>
                </div>
            </div>

            <div className="flex flex-col md:flex-row justify-center gap-6">
                {filteredPlans.map((plan) => (
                    <div
                        key={plan.id}
                        className="bg-white border border-[#C2D3DD73] rounded-xl shadow-lg p-6 w-full md:w-1/3"
                        style={{ backgroundImage: `url(${plan.bg})` }}
                    >
                        <div className="flex justify-center mb-4 relative">
                            <img
                                src={plan.bg}
                                alt=""
                                className="absolute top-0 left-0 w-full h-full object-cover opacity-50 rounded-xl"
                            />
                        </div>
                        <img
                            src={plan.img}
                            alt=""
                            className="mb-4 p-3 border h-16  rounded-3xl"
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
                                    Everyting in the standard plan
                                </li>
                                <li>Personalized roadmap course</li>
                                <li>Weekly live sessions</li>
                                <li>Structured Accoountability plan</li>
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
                            <form action="/paystack" method="POST">
                                <input
                                    type="hidden"
                                    name="_token"
                                    value={csrfToken}
                                />

                                <input
                                    type="hidden"
                                    name="tier"
                                    value={selectedPlanDetails.tier}
                                />
                                {/* <input type="hidden" name="currency" value={currency} /> */}
                                <input
                                    type="hidden"
                                    name="amount"
                                    value={selectedPlanDetails.amount[currency]}
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

                            <form action="/stripe/create" method="POST">
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
                                    name="amount"
                                    value={selectedPlanDetails.amount[currency]}
                                />
                                <input
                                    type="hidden"
                                    name="_token"
                                    value={csrfToken}
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
                            <form action="/paypal/create-order" method="POST">
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
                                    name="amount"
                                    value={selectedPlanDetails.amount[currency]}
                                />
                                <input
                                    type="hidden"
                                    name="_token"
                                    value={csrfToken}
                                />
                                <input
                                    type="hidden"
                                    name="currency"
                                    value={currency}
                                />
                                <button
                                    type="submit"
                                    className="bg-cyan-500 hover:bg-cyan-800 py-3 rounded text-center font-semibold w-full"
                                >
                                    Pay with Paypal
                                </button>
                            </form>
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
                                <span className="inline-block mx-2 bg-gray-300 rounded-md p-1">
                                    <img
                                        src="/icons/paypal.png"
                                        alt="Paystack"
                                        className="h-4"
                                    />
                                </span>
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
