"use client";

import { useState } from "react";

type Hotel = {
  id: number;
  name: string;
  price: number;
  station: string;
  walk: number;
};

type Props = {
  data: Hotel[];
};

export default function HotelTable({ data }: Props) {
  const [likes, setLikes] = useState<{ [key: number]: number }>({});
  const [loves, setLoves] = useState<{ [key: number]: number }>({});

  const addLike = (id: number) => {
    setLikes({ ...likes, [id]: (likes[id] || 0) + 1 });
  };

  const totalLove = Object.values(loves).reduce((sum, v) => sum + v, 0);

  const addLove = (id: number) => {
    if (loves[id]) return;
    if (totalLove >= 3) return;

    setLoves({ ...loves, [id]: (loves[id] || 0) + 1 });
  };

  return (
    <table className="mt-4 w-full border">
      <thead>
        <tr>
          <th className="border p-2">名前</th>
          <th className="border p-2">料金</th>
          <th className="border p-2">立地</th>
          <th className="border p-2">評価</th>
        </tr>
      </thead>
      <tbody>
        {data.map((item) => (
          <tr key={item.id}>
            <td className="border p-2">{item.name}</td>
            <td className="border p-2">¥{item.price}</td>
            <td className="border p-2">
              {item.station} 徒歩{item.walk}分
            </td>
            <td className="border p-2">
              <button onClick={() => addLove(item.id)}>❤️</button>
              {loves[item.id] || 0}
              <button onClick={() => addLike(item.id)} className="ml-2">
                👍
              </button>
              {likes[item.id] || 0}
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  );
}
